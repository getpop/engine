<?php
namespace PoP\Engine\ModuleFilters;
use PoP\Engine\Facades\ModuleFilterManager;

interface ModuleFilterInterface
{
    public function getName();
    public function excludeModule(array $module, &$props);
    public function removeExcludedSubmodules(array $module, $submodules);
    public function prepareForPropagation(array $module, &$props);
    public function restoreFromPropagation(array $module, &$props);
}
