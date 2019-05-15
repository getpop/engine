<?php
namespace PoP\Engine\ModuleFilters;
use PoP\Engine\Facades\ModuleFilterManager;

interface ModuleFilterInterface
{
    public function getName();
    public function excludeModule($module, &$props);
    public function removeExcludedSubmodules($module, $submodules);
    public function prepareForPropagation($module, &$props);
    public function restoreFromPropagation($module, &$props);
}
