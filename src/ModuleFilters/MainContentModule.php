<?php
namespace PoP\Engine\ModuleFilters;
use PoP\Hooks\Facades\HooksAPI;


class MainContentModule extends AbstractModuleFilter
{
    const NAME = 'maincontentmodule';
    
    public function getName()
    {
        return self::NAME;
    }

    public function excludeModule(array $module, &$props)
    {
        $vars = \PoP\Engine\Engine_Vars::getVars();
        return $vars['maincontentmodule'] != $module;
    }
}
