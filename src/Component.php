<?php
namespace PoP\Engine;

// use PoP\Hooks\Facades\HooksAPI;
use PoP\Root\Component\AbstractComponent;
use PoP\Root\Component\YAMLServicesTrait;
use PoP\Root\Component\PHPConfigurableServicesTrait;
use PoP\Engine\Component\InstantiateNamespaceClassesTrait;

/**
 * Class required to check if this component exists and is active
 */
class Component extends AbstractComponent
{
    use YAMLServicesTrait;
    use PHPConfigurableServicesTrait;
    use InstantiateNamespaceClassesTrait;

    // protected static function initializeServices()
    // {
    //     HooksAPI::getInstance()->addAction(
    //         'popcms:init', 
    //         array($this, 'init'), 
    //         PHP_INT_MAX
    //     );
    // }

    /**
     * Initialize services
     */
    public static function init()
    {
        parent::init();
        self::initYAMLServices(dirname(__DIR__));
        self::instantiateNamespaceClasses([
            __NAMESPACE__.'\HookImplementations',
        ]);
    }

    /**
     * Initialize Service Configuration
     *
     * @return void
     */
    public static function boot()
    {
        self::initPHPServiceConfiguration(dirname(__DIR__));
    }
}
