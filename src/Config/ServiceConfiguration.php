<?php
namespace PoP\Engine\Config;

use PoP\Root\Component\PHPServiceConfigurationTrait;
use PoP\ComponentModel\Container\ContainerBuilderUtils;
use PoP\Engine\DirectiveResolvers\SetSelfAsExpressionDirectiveResolver;
use PoP\CacheControl\DirectiveResolvers\AbstractCacheControlDirectiveResolver;

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

        // Inject the mandatory root directives
        ContainerBuilderUtils::injectValuesIntoService(
            'dataloading_engine',
            'addMandatoryRootDirectiveClass',
            SetSelfAsExpressionDirectiveResolver::class
        );
        ContainerBuilderUtils::injectValuesIntoService(
            'dataloading_engine',
            'addMandatoryRootDirectives',
            [
                AbstractCacheControlDirectiveResolver::getDirectiveName(),
            ]
        );
    }
}
