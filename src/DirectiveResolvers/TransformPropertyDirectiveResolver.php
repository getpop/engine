<?php
namespace PoP\Engine\DirectiveResolvers;

use PoP\FieldQuery\QueryHelpers;
use PoP\ComponentModel\GeneralUtils;
use PoP\ComponentModel\DataloaderInterface;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\Engine\Dataloading\Expressions;
use PoP\ComponentModel\Schema\TypeCastingHelpers;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\FieldResolvers\PipelinePositions;
use PoP\ComponentModel\FieldResolvers\AbstractFieldResolver;
use PoP\ComponentModel\FieldResolvers\FieldResolverInterface;
use PoP\ComponentModel\Facades\Schema\FieldQueryInterpreterFacade;

class TransformPropertyDirectiveResolver extends AbstractGlobalDirectiveResolver
{
    public const DIRECTIVE_NAME = 'transformProperty';
    public static function getDirectiveName(): string {
        return self::DIRECTIVE_NAME;
    }

    /**
     * By default, this directive goes after ResolveValueAndMerge
     *
     * @return void
     */
    public function getPipelinePosition(): string
    {
        return PipelinePositions::BACK;
    }

    /**
     * Most likely, this directive can be executed several times
     *
     * @return boolean
     */
    public function canExecuteMultipleTimesInField(): bool
    {
        return true;
    }

    public function getSchemaDirectiveArgs(FieldResolverInterface $fieldResolver): array
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return [
            [
                SchemaDefinition::ARGNAME_NAME => 'function',
                SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_STRING,
                SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Function to execute on the affected fields', 'component-model'),
                SchemaDefinition::ARGNAME_MANDATORY => true,
            ],
            [
                SchemaDefinition::ARGNAME_NAME => 'addParams',
                SchemaDefinition::ARGNAME_TYPE => TypeCastingHelpers::combineTypes(SchemaDefinition::TYPE_ARRAY, SchemaDefinition::TYPE_MIXED),
                SchemaDefinition::ARGNAME_DESCRIPTION => sprintf(
                    $translationAPI->__('Parameters to inject to the function. The value of the affected field can be provided under special expression `%s`', 'component-model'),
                    QueryHelpers::getExpressionQuery(Expressions::NAME_VALUE)
                ),
            ],
            [
                SchemaDefinition::ARGNAME_NAME => 'target',
                SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_STRING,
                SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Property from the current object where to store the results of the function. If not provided, it uses the same as the affected field. If the result must not be stored, pass an empty value', 'component-model'),
            ],
        ];
    }

    public function resolveDirective(DataloaderInterface $dataloader, FieldResolverInterface $fieldResolver, array &$resultIDItems, array &$idsDataFields, array &$dbItems, array &$previousDBItems, array &$variables, array &$messages, array &$dbErrors, array &$dbWarnings, array &$schemaErrors, array &$schemaWarnings, array &$schemaDeprecations)
    {
        $this->regenerateAndExecuteFunction($dataloader, $fieldResolver, $resultIDItems, $idsDataFields, $dbItems, $previousDBItems, $variables, $messages, $dbErrors, $dbWarnings, $schemaErrors, $schemaWarnings, $schemaDeprecations);
    }

    /**
     * Execute a function on the affected field
     *
     * @param FieldResolverInterface $fieldResolver
     * @param array $resultIDItems
     * @param array $idsDataFields
     * @param array $dbItems
     * @param array $dbErrors
     * @param array $dbWarnings
     * @param array $schemaErrors
     * @param array $schemaWarnings
     * @param array $schemaDeprecations
     * @return void
     */
    protected function regenerateAndExecuteFunction(DataloaderInterface $dataloader, FieldResolverInterface $fieldResolver, array &$resultIDItems, array &$idsDataFields, array &$dbItems, array &$previousDBItems, array &$variables, array &$messages, array &$dbErrors, array &$dbWarnings, array &$schemaErrors, array &$schemaWarnings, array &$schemaDeprecations)
    {
        $function = $this->directiveArgsForSchema['function'];
        $addParams = $this->directiveArgsForSchema['addParams'] ?? [];
        $target = $this->directiveArgsForSchema['target'];

        $translationAPI = TranslationAPIFacade::getInstance();
        $fieldQueryInterpreter = FieldQueryInterpreterFacade::getInstance();

        // Maybe re-generate the function: Inject the provided `$addParams` to the fieldArgs already declared in the query
        if ($addParams) {
            $functionName = $fieldQueryInterpreter->getFieldName($function);
            $functionArgElems = array_merge(
                $fieldQueryInterpreter->extractFieldArguments($fieldResolver, $function),
                $addParams
            );
            $function = $fieldQueryInterpreter->getField($functionName, $functionArgElems);
        }
        $dbKey = $dataloader->getDatabaseKey();

        // Get the value from the object
        foreach ($idsDataFields as $id => $dataFields) {
            foreach ($dataFields['direct'] as $field) {
                $fieldOutputKey = $fieldQueryInterpreter->getFieldOutputKey($field);

                // Validate that the property exists
                $isValueInDBItems = array_key_exists($fieldOutputKey, $dbItems[(string)$id] ?? []);
                if (!$isValueInDBItems && !array_key_exists($fieldOutputKey, $previousDBItems[$dbKey][(string)$id] ?? [])) {
                    if ($fieldOutputKey != $field) {
                        $dbErrors[(string)$id][$this->directive][] = sprintf(
                            $translationAPI->__('Field \'%s\' (with output key \'%s\') hadn\'t been set for object with ID \'%s\', so it can\'t be transformed', 'component-model'),
                            $field,
                            $fieldOutputKey,
                            $id
                        );
                    } else {
                        $dbErrors[(string)$id][$this->directive][] = sprintf(
                            $translationAPI->__('Field \'%s\' hadn\'t been set for object with ID \'%s\', so it can\'t be transformed', 'component-model'),
                            $fieldOutputKey,
                            $id
                        );
                    }
                    continue;
                }

                // Place all the reserved variables into the `$variables` context: $value
                $this->addVariableValuesForResultItemInContext($dataloader, $fieldResolver, $id, $field, $resultIDItems, $dbItems, $previousDBItems, $variables, $messages, $dbErrors, $dbWarnings, $schemaErrors, $schemaWarnings, $schemaDeprecations);

                // Generate the fieldArgs from combining the query with the values in the context, through $variables
                $expressions = $this->getExpressionsForResultItem($id, $variables, $messages);
                list(
                    $schemaValidField,
                    $schemaFieldName,
                    $schemaFieldArgs,
                    $schemaDBErrors,
                    $schemaDBWarnings
                ) = $fieldQueryInterpreter->extractFieldArgumentsForSchema($fieldResolver, $function, $variables);

                // Place the errors not under schema but under DB, since they may change on a resultItem by resultItem basis
                if ($schemaDBWarnings) {
                    foreach ($schemaDBWarnings as $warningMessage) {
                        $dbWarnings[(string)$id][$this->directive][] = sprintf(
                            $translationAPI->__('Warning validating function \'%s\' on object with ID \'%s\' and field with output key \'%s\': %s)', 'component-model'),
                            $function,
                            $id,
                            $fieldOutputKey,
                            $warningMessage
                        );
                    }
                }
                if ($schemaDBErrors) {
                    foreach ($schemaDBErrors as $errorMessage) {
                        $dbErrors[(string)$id][$this->directive][] = sprintf(
                            $translationAPI->__('Error validating function \'%s\' on object with ID \'%s\' and field with output key \'%s\': %s)', 'component-model'),
                            $function,
                            $id,
                            $fieldOutputKey,
                            $errorMessage
                        );
                    }
                    if ($fieldOutputKey != $field) {
                        $dbErrors[(string)$id][$this->directive][] = sprintf(
                            $translationAPI->__('Transformation of field \'%s\' (with output key \'%s\') on object with ID \'%s\' can\'t be executed due to previous errors', 'component-model'),
                            $field,
                            $fieldOutputKey,
                            $id
                        );
                    } else {
                        $dbErrors[(string)$id][$this->directive][] = sprintf(
                            $translationAPI->__('Transformation of field \'%s\' on object with ID \'%s\' can\'t be executed due to previous errors', 'component-model'),
                            $fieldOutputKey,
                            $id
                        );
                    }
                    continue;
                }

                // Execute the function
                // Because the function was dynamically created, we must indicate to validate the schema when doing ->resolveValue
                $options = [
                    AbstractFieldResolver::OPTION_VALIDATE_SCHEMA_ON_RESULT_ITEM => true,
                ];
                $functionValue = $fieldResolver->resolveValue($resultIDItems[(string)$id], $function, $variables, $expressions, $options);

                // If there was an error (eg: a missing mandatory argument), then the function will be of type Error
                if (GeneralUtils::isError($functionValue)) {
                    $error = $functionValue;
                    $dbErrors[(string)$id][$this->directive][] = sprintf(
                        $translationAPI->__('Transformation of property \'%s\' on object with ID \'%s\' failed due to error: %s', 'component-model'),
                        $fieldOutputKey,
                        $id,
                        $error->getErrorMessage()
                    );
                    continue;
                }

                // Store the results:
                // If there is a target specified, use it
                // If the specified target is empty, then do not store the results
                // If no target was specified, use the same affected field
                $functionTarget = $target ?? $fieldOutputKey;
                if ($functionTarget) {
                    $dbItems[(string)$id][$functionTarget] = $functionValue;
                }
            }
        }
    }

    /**
     * Place all the reserved variables into the `$variables` context
     *
     * @param DataloaderInterface $dataloader
     * @param FieldResolverInterface $fieldResolver
     * @param [type] $id
     * @param string $field
     * @param array $resultIDItems
     * @param array $dbItems
     * @param array $dbErrors
     * @param array $dbWarnings
     * @param array $schemaErrors
     * @param array $schemaWarnings
     * @param array $schemaDeprecations
     * @param array $previousDBItems
     * @param array $variables
     * @param array $messages
     * @return void
     */
    protected function addVariableValuesForResultItemInContext(DataloaderInterface $dataloader, FieldResolverInterface $fieldResolver, $id, string $field, array &$resultIDItems, array &$dbItems, array &$previousDBItems, array &$variables, array &$messages, array &$dbErrors, array &$dbWarnings, array &$schemaErrors, array &$schemaWarnings, array &$schemaDeprecations)
    {
        $fieldQueryInterpreter = FieldQueryInterpreterFacade::getInstance();
        $fieldOutputKey = $fieldQueryInterpreter->getFieldOutputKey($field);
        $isValueInDBItems = array_key_exists($fieldOutputKey, $dbItems[(string)$id] ?? []);
        $dbKey = $dataloader->getDatabaseKey();
        $value = $isValueInDBItems ?
            $dbItems[(string)$id][$fieldOutputKey] :
            $previousDBItems[$dbKey][(string)$id][$fieldOutputKey];
        $this->addExpressionForResultItem($id, 'value', $value, $messages);
    }
}