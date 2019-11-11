<?php
namespace PoP\Engine\DirectiveResolvers;
use PoP\ComponentModel\FieldResolvers\FieldResolverInterface;

trait FilterIDsSatisfyingConditionDirectiveResolverTrait
{
    protected function getIdsSatisfyingCondition(FieldResolverInterface $fieldResolver, array &$resultIDItems, array &$idsDataFields, array &$variables, array &$messages, array &$dbErrors, array &$dbWarnings)
    {
        // Check the condition field. If it is satisfied, then skip those fields
        $idsSatisfyingCondition = [];
        foreach (array_keys($idsDataFields) as $id) {
            // Validate directive args for the resultItem
            $expressions = $this->getVariablesForResultItem($id, $variables, $messages);
            $resultItem = $resultIDItems[$id];
            list(
                $resultItemValidDirective,
                $resultItemDirectiveName,
                $resultItemDirectiveArgs
            ) = $this->dissectAndValidateDirectiveForResultItem($fieldResolver, $resultItem, $variables, $expressions, $dbErrors, $dbWarnings);
            // Check that the directive is valid. If it is not, $dbErrors will have the error already added
            if (is_null($resultItemValidDirective)) {
                continue;
            }
            // $resultItemDirectiveArgs has all the right directiveArgs values. Now we can evaluate on it
            if ($resultItemDirectiveArgs['if']) {
                $idsSatisfyingCondition[] = $id;
            }
        }
        return $idsSatisfyingCondition;
    }
}
