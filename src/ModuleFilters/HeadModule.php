<?php
namespace PoP\Engine\ModuleFilters;

class HeadModule extends AbstractModuleFilter
{
    const MODULEFILTER_HEADMODULE = 'headmodule';

    public function getName()
    {
        return self::MODULEFILTER_HEADMODULE;
    }

    public function excludeModule($module, &$props)
    {
        $vars = \PoP\Engine\Engine_Vars::getVars();
        return $vars['headmodule'] != $module;
    }
}
