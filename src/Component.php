<?php
namespace PoP\Engine;

use PoP\Root\Component\ConfigurableServicesTrait;
use PoP\Root\Component\InstantiateNamespaceClassesTrait;

/**
 * Class required to check if this component exists and is active
 */
class Component
{
    use ConfigurableServicesTrait;
    use InstantiateNamespaceClassesTrait;

    /**
     * Initialize services
     */
    public static function init()
    {
        self::initServiceConfiguration(dirname(__DIR__));
        self::instantiateNamespaceClasses([
            __NAMESPACE__.'\HookImplementations',
        ]);
    }
}
