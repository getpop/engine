<?php
namespace PoP\Engine\ModuleFilters;
use PoP\Hooks\Facades\HooksAPI;


class MainContentModule extends AbstractModuleFilter
{
    const MODULEFILTER_MAINCONTENTMODULE = 'maincontentmodule';
    
    public function getName()
    {
        return self::MODULEFILTER_MAINCONTENTMODULE;
    }

    public function excludeModule($module, &$props)
    {
        $vars = \PoP\Engine\Engine_Vars::getVars();
        return $vars['maincontentmodule'] != $module;
    }
}
