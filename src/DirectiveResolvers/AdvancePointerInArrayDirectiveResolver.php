<?php
namespace PoP\Engine\DirectiveResolvers;

use Exception;
use PoP\Engine\Misc\OperatorHelpers;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\Feedback\Tokens;

class AdvancePointerInArrayDirectiveResolver extends AbstractApplyNestedDirectivesOnArrayItemsDirectiveResolver
{
    public const DIRECTIVE_NAME = 'advancePointerInArray';
    public static function getDirectiveName(): string
    {
        return self::DIRECTIVE_NAME;
    }

    /**
     * Do not allow dynamic fields
     *
     * @return bool
     */
    protected function disableDynamicFieldsFromDirectiveArgs(): bool
    {
        return true;
    }

    public function getSchemaDirectiveDescription(TypeResolverInterface $typeResolver): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('Apply all composed directives on the element found under the \'path\' parameter in the affected array object', 'component-model');
    }

    public function getSchemaDirectiveArgs(TypeResolverInterface $typeResolver): array
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return array_merge(
            [
                [
                    SchemaDefinition::ARGNAME_NAME => 'path',
                    SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_STRING,
                    SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Path to the element in the array', 'component-model'),
                    SchemaDefinition::ARGNAME_MANDATORY => true,
                ],
            ],
            parent::getSchemaDirectiveArgs($typeResolver)
        );
    }

    /**
     * Directly point to the element under the specified path
     *
     * @param array $array
     * @return void
     */
    protected function getArrayItems(array &$array, $id, string $field, TypeResolverInterface $typeResolver, array &$resultIDItems, array &$dbItems, array &$previousDBItems, array &$variables, array &$messages, array &$dbErrors, array &$dbWarnings, array &$dbDeprecations): ?array
    {
        $path = $this->directiveArgsForSchema['path'];

        // If the path doesn't exist, add the error and return
        try {
            $arrayItemPointer = OperatorHelpers::getPointerToArrayItemUnderPath($array, $path);
        } catch (Exception $e) {
            // Add an error and return null
            if (!is_null($dbErrors)) {
                $dbErrors[(string)$id][] = [
                    Tokens::PATH => [$this->directive],
                    Tokens::MESSAGE => $e->getMessage(),
                ];
            }
            return null;
        }

        // Success accessing the element under that path
        return [
            $path => &$arrayItemPointer,
        ];
    }
}
