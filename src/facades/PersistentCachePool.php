<?php
namespace PoP\Engine\Facades;

use Psr\Cache\CacheItemPoolInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class PersistentCacheItemPool
{
    public static function getInstance(): CacheItemPoolInterface
    {
        return ContainerBuilderFactory::getInstance()->get('\PoP\Engine\Contracts\PersistentCacheItemPool');
    }
}
