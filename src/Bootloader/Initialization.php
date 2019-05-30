<?php
namespace PoP\Engine\Bootloader;

use PoP\Root\Managers\ComponentManager;

class Initialization
{
    public static function init()
    {
        // Boot all the components
        ComponentManager::boot();
    }
}
