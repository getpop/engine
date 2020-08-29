<?php

declare(strict_types=1);

namespace PoP\Engine\Config;

use PoP\Root\Component\PHPServiceConfigurationTrait;
use PoP\ComponentModel\Container\ContainerBuilderUtils;
use PoP\Engine\DirectiveResolvers\SetSelfAsExpressionDirectiveResolver;
use PoP\CacheControl\DirectiveResolvers\CacheControlDirectiveResolver;
use PoP\CacheControl\Component as CacheControlComponent;
use PoP\Engine\ComponentConfiguration;

class ServiceConfiguration
{
    use PHPServiceConfigurationTrait;

    protected static function configure(): void
    {
        // Add ModuleFilters to the ModuleFilterManager
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
            'addMandatoryDirectiveClass',
            SetSelfAsExpressionDirectiveResolver::class
        );
        if (ComponentConfiguration::addMandatoryCacheControlDirective()) {
            static::configureCacheControl();
        }
    }

    public static function configureCacheControl()
    {
        if (CacheControlComponent::isEnabled() && $_SERVER['REQUEST_METHOD'] == 'GET') {
            ContainerBuilderUtils::injectValuesIntoService(
                'dataloading_engine',
                'addMandatoryDirectives',
                [
                    CacheControlDirectiveResolver::getDirectiveName(),
                ]
            );
        }
    }
}
