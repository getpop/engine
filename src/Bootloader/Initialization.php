<?php
namespace PoP\Engine\Bootloader;

use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\Root\Managers\ComponentManager;

class Initialization
{
    public static function init()
    {
        // Boot all the components
        ComponentManager::boot();

        $hooksAPI = HooksAPIFacade::getInstance();
        $hooksAPI->addAction(
            'popcms:boot',
            function() {
                // Boot all the components
                ComponentManager::earlyBoot();
            },
            5
        );

        $hooksAPI->addAction(
            'popcms:boot',
            function() {
                // Boot all the components
                ComponentManager::reallyBoot();
            },
            15
        );
    }
}
