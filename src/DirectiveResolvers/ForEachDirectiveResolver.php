<?php
namespace PoP\Engine\DirectiveResolvers;

use PoP\ComponentModel\GeneralUtils;
use PoP\Engine\Dataloading\Expressions;
use PoP\ComponentModel\DataloaderInterface;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\FieldResolvers\AbstractFieldResolver;
use PoP\ComponentModel\FieldResolvers\FieldResolverInterface;
use PoP\ComponentModel\Facades\Schema\FeedbackMessageStoreFacade;
use PoP\ComponentModel\Facades\Schema\FieldQueryInterpreterFacade;
use PoP\ComponentModel\Feedback\Tokens;

class ForEachDirectiveResolver extends AbstractApplyNestedDirectivesOnArrayItemsDirectiveResolver
{
    public const DIRECTIVE_NAME = 'forEach';
    public static function getDirectiveName(): string {
        return self::DIRECTIVE_NAME;
    }

    public function getSchemaDirectiveDescription(FieldResolverInterface $fieldResolver): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('Iterate all affected array items and execute the nested directives on them', 'component-model');
    }

    public function getSchemaDirectiveArgs(FieldResolverInterface $fieldResolver): array
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return array_merge(
            parent::getSchemaDirectiveArgs($fieldResolver),
            [
                [
                    SchemaDefinition::ARGNAME_NAME => 'if',
                    SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_BOOL,
                    SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('If provided, iterate only those items that satisfy this condition `%s`', 'component-model'),
                ],
            ]
        );
    }

    public function getSchemaDirectiveExpressions(FieldResolverInterface $fieldResolver): array
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return [
            Expressions::NAME_KEY => $translationAPI->__('Key of the array element from the current iteration', 'component-model'),
            Expressions::NAME_VALUE => $translationAPI->__('Value of the array element from the current iteration', 'component-model'),
        ];
    }

    /**
     * Iterate on all items from the array
     *
     * @param array $value
     * @return void
     */
    protected function getArrayItems(array &$array, $id, string $field, DataloaderInterface $dataloader, FieldResolverInterface $fieldResolver, array &$resultIDItems, array &$dbItems, array &$previousDBItems, array &$variables, array &$messages, array &$dbErrors, array &$dbWarnings): ?array
    {
        if ($if = $this->directiveArgsForSchema['if']) {
            // If it is a field, execute the function against all the values in the array
            // Those that satisfy the condition stay, the others are filtered out
            // We must add each item in the array as expression `%value%`, over which the if function can be evaluated
            $fieldQueryInterpreter = FieldQueryInterpreterFacade::getInstance();
            if ($fieldQueryInterpreter->isFieldArgumentValueAField($if)) {
                $options = [
                    AbstractFieldResolver::OPTION_VALIDATE_SCHEMA_ON_RESULT_ITEM => true,
                ];
                $arrayItems = [];
                foreach ($array as $key => $value) {
                    $this->addExpressionForResultItem($id, Expressions::NAME_KEY, $key, $messages);
                    $this->addExpressionForResultItem($id, Expressions::NAME_VALUE, $value, $messages);
                    $expressions = $this->getExpressionsForResultItem($id, $variables, $messages);
                    $resolvedValue = $fieldResolver->resolveValue($resultIDItems[(string)$id], $if, $variables, $expressions, $options);
                    // Merge the dbWarnings, if any
                    $feedbackMessageStore = FeedbackMessageStoreFacade::getInstance();
                    if ($resultItemDBWarnings = $feedbackMessageStore->retrieveAndClearResultItemDBWarnings($id)) {
                        $dbWarnings[$id] = array_merge(
                            $dbWarnings[$id] ?? [],
                            $resultItemDBWarnings
                        );
                    }
                    if (GeneralUtils::isError($resolvedValue)) {
                        // Show the error message, and return nothing
                        $error = $resolvedValue;
                        $dbErrors[(string)$id][] = [
                            Tokens::PATH => [$this->directive],
                            Tokens::MESSAGE => sprintf(
                                $this->translationAPI->__('Executing field \'%s\' on object with ID \'%s\' produced error: %s. Setting expression \'%s\' was ignored', 'pop-component-model'),
                                $value,
                                $id,
                                $error->getErrorMessage(),
                                $key
                            ),
                        ];
                        continue;
                    }
                    // Evaluate it
                    if ($resolvedValue) {
                        $arrayItems[$key] = $value;
                    }
                }
                return $arrayItems;
            }
        }
        return $array;
    }
}
