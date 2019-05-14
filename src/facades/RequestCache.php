<?php
namespace PoP\Engine\Facades;

use PoP\Engine\Cache\CacheInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class RequestCache
{
    public static function getInstance(): CacheInterface
    {
        return ContainerBuilderFactory::getInstance()->get('\PoP\Engine\Contracts\RequestCache');
    }
}
