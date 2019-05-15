<?php
namespace PoP\Engine;

use PoP\Root\Component\YAMLConfigurableServicesTrait;
use PoP\Root\Component\PHPConfigurableServicesTrait;
use PoP\Engine\Component\InstantiateNamespaceClassesTrait;

/**
 * Class required to check if this component exists and is active
 */
class Component
{
    use YAMLConfigurableServicesTrait;
    use PHPConfigurableServicesTrait;
    use InstantiateNamespaceClassesTrait;

    /**
     * Initialize services
     */
    public static function init()
    {
        self::initYAMLServiceConfiguration(dirname(__DIR__));
        self::initPHPServiceConfiguration(dirname(__DIR__));
        self::instantiateNamespaceClasses([
            __NAMESPACE__.'\HookImplementations',
        ]);
    }
}
