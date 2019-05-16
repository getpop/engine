<?php
namespace PoP\Engine\Facades;

use Psr\Cache\CacheItemPoolInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class PersistentCacheItemPool
{
    public static function getInstance(): CacheItemPoolInterface
    {
        return ContainerBuilderFactory::getInstance()->get('persistent_cache_item_pool');
    }
}
