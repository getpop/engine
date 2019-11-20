<?php
namespace PoP\Engine\DirectiveResolvers;

use PoP\FieldQuery\QueryHelpers;
use PoP\ComponentModel\DataloaderInterface;
use PoP\Engine\Dataloading\Expressions;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\FieldResolvers\PipelinePositions;
use PoP\ComponentModel\FieldResolvers\FieldResolverInterface;
use PoP\ComponentModel\DirectiveResolvers\AbstractGlobalDirectiveResolver;

class SetSelfAsExpressionDirectiveResolver extends AbstractGlobalDirectiveResolver
{
    const DIRECTIVE_NAME = 'setSelfAsExpression';
    public static function getDirectiveName(): string {
        return self::DIRECTIVE_NAME;
    }

    /**
     * This directive must go at the very beginning
     *
     * @return void
     */
    public function getPipelinePosition(): string
    {
        return PipelinePositions::FRONT;
    }

    public function getSchemaDirectiveDescription(FieldResolverInterface $fieldResolver): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return sprintf(
            $translationAPI->__('Place the current object\'s data under expression `%s`, making it accessible to fields and directives through helper function `getPropertyFromSelf`', 'component-model'),
            QueryHelpers::getExpressionQuery(Expressions::NAME_SELF)
        );
    }

    public function getSchemaDirectiveExpressions(FieldResolverInterface $fieldResolver): array
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return [
            Expressions::NAME_SELF => $translationAPI->__('Object containing all properties for the current object, fetched either in the current or a previous iteration. These properties can be accessed through helper function `getSelfProp`', 'component-model'),
        ];
    }

    /**
     * Copy the data under the relational object into the current object
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
    public function resolveDirective(DataloaderInterface $dataloader, FieldResolverInterface $fieldResolver, array &$idsDataFields, array &$succeedingPipelineIDsDataFields, array &$resultIDItems, array &$dbItems, array &$previousDBItems, array &$variables, array &$messages, array &$dbErrors, array &$dbWarnings, array &$schemaErrors, array &$schemaWarnings, array &$schemaDeprecations)
    {
        // The name of the variable is always set to "self", accessed as $self
        $dbKey = $dataloader->getDatabaseKey();
        foreach (array_keys($idsDataFields) as $id) {
            // Make an array of references, pointing to the position of the current object in arrays $dbItems and $previousDBItems;
            // It is extremeley important to make it by reference, so that when the 2 variables are updated later on during the current iteration,
            // the new values are immediately available to all fields and directives executed later during the same iteration
            $value = [
                'dbItems' => &$dbItems[(string)$id],
                'previousDBItems' => &$previousDBItems[$dbKey][(string)$id],
            ];
            $this->addExpressionForResultItem($id, Expressions::NAME_SELF, $value, $messages);
        }
    }
}
