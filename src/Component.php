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
        ContainerBuilderUtils::attachFieldValueResolversFromNamespace(__NAMESPACE__.'\\FieldValueResolvers');

        // Initialize directive resolvers, and then re-attach using the right priorities
        ContainerBuilderUtils::attachDirectiveResolversFromNamespace(__NAMESPACE__.'\\DirectiveResolvers');
        self::setDirectiveResolverPriorities();
    }

    /**
     * Sets the right priority for the directive resolvers
     *
     * @return void
     */
    protected static function setDirectiveResolverPriorities()
    {
        $classes = [
            NoCacheCacheControlDirectiveResolver::class,
            OneYearCacheControlDirectiveResolver::class,
        ];
        foreach ($classes as $class) {
            $class::attach(AttachableExtensionGroups::FIELDDIRECTIVERESOLVERS, 20);
        }
    }
}
