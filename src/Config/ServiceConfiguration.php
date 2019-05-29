<?php
namespace PoP\Engine\Config;

use PoP\Root\Container\ContainerBuilderFactory;
use Symfony\Component\DependencyInjection\Reference;

class ServiceConfiguration
{
    public static function init()
    {
        $containerBuilder = ContainerBuilderFactory::getInstance();
        
        // Add ModuleFilters to the ModuleFilterManager
        $definition = $containerBuilder->getDefinition('module_filter_manager');
        $definition->addMethodCall('add', [new Reference('module_filters.head_module')]);
        $definition->addMethodCall('add', [new Reference('module_filters.module_paths')]);
        $definition->addMethodCall('add', [new Reference('module_filters.lazy')]);
        $definition->addMethodCall('add', [new Reference('module_filters.main_content_module')]);
    }
}