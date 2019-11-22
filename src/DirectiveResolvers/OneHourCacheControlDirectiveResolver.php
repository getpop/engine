<?php
namespace PoP\Engine\DirectiveResolvers;

use PoP\ComponentModel\DataloaderInterface;
use PoP\ComponentModel\FieldResolvers\FieldResolverInterface;
use PoP\CacheControl\DirectiveResolvers\AbstractCacheControlDirectiveResolver;

/**
 * Because it doesn't implement `getFieldNamesToApplyTo`, this will be the default configuration for all fields
 */
class OneHourCacheControlDirectiveResolver extends AbstractCacheControlDirectiveResolver
{
    public function getMaxAge(DataloaderInterface $dataloader, FieldResolverInterface $fieldResolver, array &$resultIDItems, array &$idsDataFields, array &$dbItems, array &$previousDBItems, array &$variables, array &$messages, array &$dbErrors, array &$dbWarnings, array &$schemaErrors, array &$schemaWarnings, array &$schemaDeprecations): int
    {
        // Cache for 1 hour (3600 seconds)
        return 3600;
    }
}
