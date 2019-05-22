<?php
namespace PoP\Engine\ModuleFilters;

class HeadModule extends AbstractModuleFilter
{
    const NAME = 'headmodule';

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
