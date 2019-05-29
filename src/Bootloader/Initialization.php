<?php
namespace PoP\Engine\Bootloader;

use PoP\Root\Component\ComponentManager;

class Initialization
{
    public static function init()
    {
        // Boot all the components
        ComponentManager::boot();

        // Instantiate all those immediately-required files
        InstantiateNamespaceClasses::init();
    }
}
