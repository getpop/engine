<?php
namespace PoP\Engine\DirectiveResolvers;

use Exception;
use PoP\API\Misc\OperatorHelpers;
use PoP\ComponentModel\DataloaderInterface;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\FieldResolvers\FieldResolverInterface;

class AdvancePointerInArrayDirectiveResolver extends AbstractApplyNestedDirectivesOnArrayItemsDirectiveResolver
{
    public const DIRECTIVE_NAME = 'advancePointerInArray';
    public static function getDirectiveName(): string {
        return self::DIRECTIVE_NAME;
    }

    public function getSchemaDirectiveDescription(FieldResolverInterface $fieldResolver): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('Apply all nested directives on the element found under the \'path\' parameter in the affected array object', 'component-model');
    }

    public function getSchemaDirectiveArgs(FieldResolverInterface $fieldResolver): array
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
            parent::getSchemaDirectiveArgs($fieldResolver)
        );
    }

    /**
     * Directly point to the element under the specified path
     *
     * @param array $array
     * @return void
     */
    protected function getArrayItems(array &$array, $id, string $field, DataloaderInterface $dataloader, FieldResolverInterface $fieldResolver, array &$resultIDItems, array &$dbItems, array &$previousDBItems, array &$variables, array &$messages, array &$dbErrors, array &$dbWarnings): ?array
    {
        $path = $this->directiveArgsForSchema['path'];

        // If the path doesn't exist, add the error and return
        try {
            $arrayItemPointer = OperatorHelpers::getPointerToArrayItemUnderPath($array, $path);
        } catch (Exception $e) {
            // Add an error and return null
            if (!is_null($dbErrors)) {
                $dbErrors[(string)$id][$this->directive][] = $e->getMessage();
            }
            return null;
        }

        // Success accessing the element under that path
        return [
            $path => &$arrayItemPointer,
        ];
    }
}