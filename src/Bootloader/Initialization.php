<?php
namespace PoP\Engine\Bootloader;

use PoP\Root\Component\ComponentManager;
use PoP\Root\Container\ContainerBuilderFactory;

class Initialization
{
    public static function init()
    {
        // Compile and Cache Symfony's DependencyInjection Container Builder
        ContainerBuilderFactory::maybeCompileAndCacheContainer();

        // Boot all the components
        ComponentManager::boot();

        // Instantiate all those immediately-required files
        InstantiateNamespaceClasses::init();
    }
}
