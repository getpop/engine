<?php
namespace PoP\Engine\ModuleFilters;

use PoP\Engine\ModuleFilters\AbstractModuleFilter;

class HeadModule extends AbstractModuleFilter
{
    public const NAME = 'headmodule';
    public const URLPARAM_HEADMODULE = 'headmodule';

    public function getName()
    {
        return self::NAME;
    }

    public function excludeModule(array $module, array &$props)
    {
        $vars = \PoP\Engine\Engine_Vars::getVars();
        return $vars['headmodule'] != $module;
    }
}
