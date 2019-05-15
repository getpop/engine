<?php
namespace PoP\Engine\ModuleFilters;
use PoP\Engine\Facades\ModuleFilterManager;

abstract class AbstractModuleFilter
{
    public function __construct()
    {
        ModuleFilterManager::getInstance()->add($this);
    }
    
    abstract public function getName();

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
