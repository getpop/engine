<?php
namespace PoP\Engine\DirectiveResolvers;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\ComponentModel\DataloaderInterface;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\FieldResolvers\FieldResolverInterface;
use PoP\ComponentModel\DirectiveResolvers\AbstractGlobalDirectiveResolver;

class SkipDirectiveResolver extends AbstractGlobalDirectiveResolver
{
    use FilterIDsSatisfyingConditionDirectiveResolverTrait;

    const DIRECTIVE_NAME = 'skip';
    public static function getDirectiveName(): string {
        return self::DIRECTIVE_NAME;
    }

    public function resolveDirective(DataloaderInterface $dataloader, FieldResolverInterface $fieldResolver, array &$idsDataFields, array &$succeedingPipelineIDsDataFields, array &$resultIDItems, array &$dbItems, array &$previousDBItems, array &$variables, array &$messages, array &$dbErrors, array &$dbWarnings, array &$schemaErrors, array &$schemaWarnings, array &$schemaDeprecations)
    {
        // Check the condition field. If it is satisfied, then skip those fields
        $idsToRemove = $this->getIdsSatisfyingCondition($fieldResolver, $resultIDItems, $idsDataFields, $variables, $messages, $dbErrors, $dbWarnings);
        $this->removeDataFieldsForIDs($idsDataFields, $idsToRemove, $succeedingPipelineIDsDataFields);
    }
    public function getSchemaDirectiveDescription(FieldResolverInterface $fieldResolver): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('Include the field value in the output only if the argument \'if\' evals to `false`', 'api');
    }
    public function getSchemaDirectiveArgs(FieldResolverInterface $fieldResolver): array
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return [
            [
                SchemaDefinition::ARGNAME_NAME => 'if',
                SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_BOOL,
                SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Argument that must evaluate to `false` to include the field value in the output', 'api'),
                SchemaDefinition::ARGNAME_MANDATORY => true,
            ],
        ];
    }
}
