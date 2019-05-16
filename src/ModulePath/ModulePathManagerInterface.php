<?php
namespace PoP\Engine\ModulePath;

interface ModulePathManagerInterface
{
    public function getPropagationCurrentPath();
    public function setPropagationCurrentPath($propagation_current_path = null);
    /**
     * The `prepare` function advances the modulepath one level down, when interating into the submodules, and then calling `restore` the value goes one level up again
     */
    public function prepareForPropagation($module, &$props);
    public function restoreFromPropagation($module, &$props);
}
