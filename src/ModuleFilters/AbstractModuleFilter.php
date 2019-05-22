<?php
namespace PoP\Engine\ModuleFilters;

abstract class AbstractModuleFilter implements ModuleFilterInterface
{
    public function excludeModule(array $module, &$props)
    {
        return false;
    }

    public function removeExcludedSubmodules(array $module, $submodules)
    {
        return $submodules;
    }

    public function prepareForPropagation(array $module, &$props)
    {
    }

    public function restoreFromPropagation(array $module, &$props)
    {
    }
}
