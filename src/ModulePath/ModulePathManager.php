<?php
namespace PoP\Engine\ModulePath;

class ModulePathManager implements ModulePathManagerInterface
{
    protected $propagation_current_path = [];
    
    public function getPropagationCurrentPath()
    {
        return $this->propagation_current_path;
    }

    public function setPropagationCurrentPath($propagation_current_path = null)
    {
        $this->propagation_current_path = $propagation_current_path;
    }

    /**
     * The `prepare` function advances the modulepath one level down, when interating into the submodules, and then calling `restore` the value goes one level up again
     */
    public function prepareForPropagation($module, &$props)
    {
        // Add the module to the path
        // Prepare for the submodule, going one level down, and adding it to the current path
        // We add $module instead of the first element from $this->propagation_unsettled_paths, so that calculating $this->propagation_current_path works also when not doing ?modulepaths=...
        $this->propagation_current_path[] = $module;
    }
    public function restoreFromPropagation($module, &$props)
    {
        // Remove the module to the path
        array_pop($this->propagation_current_path);
    }
}