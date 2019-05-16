<?php
namespace PoP\Engine\HookImplementations\ModuleFilters;

use PoP\Engine\Hooks\AbstractHookImplementation;

class MainContentModule extends AbstractHookImplementation
{
    public function __construct()
    {
        parent::__construct();
        $this->hooksAPI->addAction(
            'augmentVarsProperties', 
            [$this, 'augmentVarsProperties'], 
            PHP_INT_MAX,
            1
        );
    }
    public function augmentVarsProperties($vars_in_array)
    {
        $vars = &$vars_in_array[0];

        // Function `getRouteModuleByMostAllmatchingVarsProperties` actually needs to access all values in $vars
        // Hence, calculate only at the very end
        // If filtering module by "maincontent", then calculate which is the main content module
        if ($vars['modulefilter'] == \PoP\Engine\ModuleFilters\MainContentModule::NAME) {
            $vars['maincontentmodule'] = \PoP\ModuleRouting\RouteModuleProcessorManagerFactory::getInstance()->getRouteModuleByMostAllmatchingVarsProperties(POP_PAGEMODULEGROUPPLACEHOLDER_MAINCONTENTMODULE);
        }
    }
}
