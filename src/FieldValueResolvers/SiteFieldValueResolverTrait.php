<?php
namespace PoP\Engine\FieldValueResolvers;
use PoP\ComponentModel\FieldResolvers\FieldResolverInterface;

trait SiteFieldValueResolverTrait
{
    public function resolveCanProcessResultItem(FieldResolverInterface $fieldResolver, $resultItem, string $fieldName, array $fieldArgs = []): bool
    {
        $cmsengineapi = \PoP\Engine\FunctionAPIFactory::getInstance();
        $site = $resultItem;
        // Only for the current site. For other sites must be implemented through a "multisite" package
        // The parent class will return the correct value. That's why if it is not the current site, then already return the expected error
        if ($site->getHost() != $cmsengineapi->getHost()) {
            return false;
        }
        return parent::resolveCanProcessResultItem($fieldResolver, $resultItem, $fieldName, $fieldArgs);
    }
}