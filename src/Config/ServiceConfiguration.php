<?php
namespace PoP\Engine\Config;

use PoP\Root\Container\ContainerBuilderUtils;
use PoP\Root\Container\ContainerBuilderFactory;
use PoP\Root\Component\PHPServiceConfigurationTrait;
use Symfony\Component\DependencyInjection\Reference;

class ServiceConfiguration
{
    use PHPServiceConfigurationTrait;

    protected static function configure()
    {
        $containerBuilder = ContainerBuilderFactory::getInstance();
        
        // Add ModuleFilter to the ModuleFilterManager
        $definition = $containerBuilder->getDefinition('module_filter_manager');
        $moduleFilterServiceIds = ContainerBuilderUtils::getNamespaceServiceIds('PoP\\Engine\\ModuleFilter\\Implementations');
        foreach ($moduleFilterServiceIds as $moduleFilterServiceId) {
            $definition->addMethodCall('add', [new Reference($moduleFilterServiceId)]);
        }
    }
}