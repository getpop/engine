<?php
namespace PoP\Engine\DirectiveResolvers\Guzzle;

use PoP\CacheControl\DirectiveResolvers\AbstractCacheControlDirectiveResolver;

class NoCacheCacheControlDirectiveResolver extends AbstractCacheControlDirectiveResolver
{
    public static function getFieldNamesToApplyTo(): array
    {
        return [
            'getJSON',
        ];
    }

    public function getMaxAge(): ?int
    {
        // Do not cache
        return 0;
    }
}