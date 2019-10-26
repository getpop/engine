<?php
namespace PoP\Engine\Config;

use PoP\ComponentModel\Container\ContainerBuilderUtils;
use PoP\Root\Component\PHPServiceConfigurationTrait;

class ServiceConfiguration
{
    use PHPServiceConfigurationTrait;

    protected static function configure()
    {
        // Add ModuleFilter to the ModuleFilterManager
        ContainerBuilderUtils::injectServicesIntoService(
            'module_filter_manager',
            'PoP\\Engine\\ModuleFilters',
            'add'
        );

        // Add RouteModuleProcessors to the Manager
        ContainerBuilderUtils::injectServicesIntoService(
            'route_module_processor_manager',
            'PoP\\Engine\\RouteModuleProcessors',
            'add'
        );

        ContainerBuilderUtils::injectServicesIntoService(
            'data_structure_manager',
            'PoP\\Engine\\DataStructureFormatters',
            'add'
        );
    }
}
