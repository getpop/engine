<?php
namespace PoP\Engine\Facades;

use Psr\Cache\CacheItemPoolInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class RequestCacheItemPoolFacade
{
    public static function getInstance(): CacheItemPoolInterface
    {
        return ContainerBuilderFactory::getInstance()->get('request_cache_item_pool');
    }
}
