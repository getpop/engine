<?php
namespace PoP\Engine\DirectiveResolvers;

use PoP\CacheControl\DirectiveResolvers\AbstractCacheControlDirectiveResolver;

/**
 * Because it doesn't implement `getFieldNamesToApplyTo`, this will be the default configuration for all fields
 */
class OneHourCacheControlDirectiveResolver extends AbstractCacheControlDirectiveResolver
{
    public function getMaxAge(): int
    {
        // Cache for 1 hour (3600 seconds)
        return 3600;
    }
}
