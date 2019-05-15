<?php
namespace PoP\Engine\ModuleFilters;

abstract class AbstractModuleFilter implements ModuleFilterInterface
{
    public function excludeModule($module, &$props)
    {
        return false;
    }

    public function removeExcludedSubmodules($module, $submodules)
    {
        return $submodules;
    }

    public function prepareForPropagation($module, &$props)
    {
    }

    public function restoreFromPropagation($module, &$props)
    {
    }
}
