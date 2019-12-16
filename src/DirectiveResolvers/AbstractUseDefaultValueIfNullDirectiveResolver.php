<?php
namespace PoP\Engine\DirectiveResolvers;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\Facades\Schema\FieldQueryInterpreterFacade;
use PoP\ComponentModel\DirectiveResolvers\AbstractSchemaDirectiveResolver;

abstract class AbstractUseDefaultValueIfNullDirectiveResolver extends AbstractSchemaDirectiveResolver
{
    protected abstract function getDefaultValue();

    public function resolveDirective(TypeResolverInterface $typeResolver, array &$idsDataFields, array &$succeedingPipelineIDsDataFields, array &$resultIDItems, array &$unionDBKeyIDs, array &$dbItems, array &$previousDBItems, array &$variables, array &$messages, array &$dbErrors, array &$dbWarnings, array &$schemaErrors, array &$schemaWarnings, array &$schemaDeprecations)
    {
        // Replace all the NULL results with the default value
        $fieldQueryInterpreter = FieldQueryInterpreterFacade::getInstance();
        $fieldOutputKeyCache = [];
        foreach ($idsDataFields as $id => $dataFields) {
            // Use either the default value passed under param "value" or, if this is NULL, use a predefined value
            $expressions = $this->getExpressionsForResultItem($id, $variables, $messages);
            $resultItem = $resultIDItems[$id];
            list(
                $resultItemValidDirective,
                $resultItemDirectiveName,
                $resultItemDirectiveArgs
            ) = $this->dissectAndValidateDirectiveForResultItem($typeResolver, $resultItem, $variables, $expressions, $dbErrors, $dbWarnings);
            // Check that the directive is valid. If it is not, $dbErrors will have the error already added
            if (is_null($resultItemValidDirective)) {
                continue;
            }
            // Take the default value from the directiveArgs
            $defaultValue = $resultItemDirectiveArgs['value'] ?? $this->getDefaultValue();
            foreach ($dataFields['direct'] as $field) {
                // Get the fieldOutputKey from the cache, or calculate it
                if (is_null($fieldOutputKeyCache[$field])) {
                    $fieldOutputKeyCache[$field] = $fieldQueryInterpreter->getFieldOutputKey($field);
                }
                $fieldOutputKey = $fieldOutputKeyCache[$field];
                // If it is null, replace it with the default value
                if (is_null($dbItems[$id][$fieldOutputKey])) {
                    $dbItems[$id][$fieldOutputKey] = $defaultValue;
                }
            }
        }
    }
    public function getSchemaDirectiveDescription(TypeResolverInterface $typeResolver): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('If the value of the field is `NULL`, replace it with either the value provided under arg \'value\', or with a default value configured in the directive resolver', 'api');
    }
    public function getSchemaDirectiveArgs(TypeResolverInterface $typeResolver): array
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return [
            [
                SchemaDefinition::ARGNAME_NAME => 'value',
                SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_MIXED,
                SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('If the value of the field is `NULL`, replace it with the value from this argument', 'api'),
                SchemaDefinition::ARGNAME_DEFAULT_VALUE => $this->getDefaultValue(),
            ],
        ];
    }
}
