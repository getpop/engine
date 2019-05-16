<?php
namespace PoP\Engine\Facades;

use PoP\Engine\Cache\CacheInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class PersistentCache
{
    public static function getInstance(): CacheInterface
    {
        return ContainerBuilderFactory::getInstance()->get('persistent_cache');
    }
}
