<?php
namespace PoP\Engine\ModuleFilters;

interface ModuleFilterManagerInterface
{
    public function getSelectedFilterName();
    public function getNotExcludedModuleSets();
    public function add($modulefilter);
    public function neverExclude($neverExclude);
    public function excludeModule($module, &$props);
    public function removeExcludedSubmodules($module, $submodules);
    /**
     * The `prepare` function advances the modulepath one level down, when interating into the submodules, and then calling `restore` the value goes one level up again
     */
    public function prepareForPropagation($module, &$props);
    public function restoreFromPropagation($module, &$props);
}
