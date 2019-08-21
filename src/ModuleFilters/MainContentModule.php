<?php
namespace PoP\Engine\ModuleFilters;

use PoP\ComponentModel\ModuleFilters\AbstractModuleFilter;

class MainContentModule extends AbstractModuleFilter
{
    public const NAME = 'maincontentmodule';
    
    public function getName()
    {
        return self::NAME;
    }

    public function excludeModule(array $module, array &$props)
    {
        $vars = \PoP\ComponentModel\Engine_Vars::getVars();
        return $vars['maincontentmodule'] != $module;
    }
}
