<?php
namespace PoP\Engine;

use PoP\Root\Component\YAMLServicesTrait;
use PoP\Root\Component\PHPConfigurableServicesTrait;
use PoP\Engine\Component\InstantiateNamespaceClassesTrait;

/**
 * Class required to check if this component exists and is active
 */
class Component
{
    use YAMLServicesTrait;
    use PHPConfigurableServicesTrait;
    use InstantiateNamespaceClassesTrait;

    /**
     * Initialize services
     */
    public static function init()
    {
        self::initYAMLServices(dirname(__DIR__));
        self::initPHPServiceConfiguration(dirname(__DIR__));
        self::instantiateNamespaceClasses([
            __NAMESPACE__.'\HookImplementations',
        ]);
    }
}
