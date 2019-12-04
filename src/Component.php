<?php
namespace PoP\Engine;

use PoP\Root\Component\AbstractComponent;
use PoP\Root\Component\YAMLServicesTrait;
use PoP\Engine\Config\ServiceConfiguration;
use PoP\ComponentModel\Container\ContainerBuilderUtils;
use PoP\ComponentModel\AttachableExtensions\AttachableExtensionGroups;
use PoP\Engine\DirectiveResolvers\NoCacheCacheControlDirectiveResolver;
use PoP\Engine\DirectiveResolvers\OneYearCacheControlDirectiveResolver;

/**
 * Initialize component
 */
class Component extends AbstractComponent
{
    use YAMLServicesTrait;
    // const VERSION = '0.1.0';

    /**
     * Initialize services
     */
    public static function init()
    {
        parent::init();
        self::initYAMLServices(dirname(__DIR__));
        ServiceConfiguration::init();
    }

    /**
     * Boot component
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        // Initialize classes
        ContainerBuilderUtils::instantiateNamespaceServices(__NAMESPACE__.'\\Hooks');
        ContainerBuilderUtils::attachFieldResolversFromNamespace(__NAMESPACE__.'\\FieldResolvers', false);
        ContainerBuilderUtils::attachDirectiveResolversFromNamespace(__NAMESPACE__.'\\DirectiveResolvers', false);
        if (!Environment::disableGuzzleOperators()) {
            ContainerBuilderUtils::attachFieldResolversFromNamespace(__NAMESPACE__.'\\FieldResolvers\\Guzzle', false);
            ContainerBuilderUtils::attachDirectiveResolversFromNamespace(__NAMESPACE__.'\\DirectiveResolvers\\Guzzle');
        }
    }
}
