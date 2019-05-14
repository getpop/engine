<?php
namespace PoP\Engine;

use PoP\Root\Component\ConfigurableServicesTrait;

/**
 * Class required to check if this component exists and is active
 */
class Component
{
    use ConfigurableServicesTrait;

    /**
     * Initialize services
     */
    public static function init()
    {
        self::initServiceConfiguration(dirname(__DIR__));
    }
}
