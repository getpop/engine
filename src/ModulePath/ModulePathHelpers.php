<?php
namespace PoP\Engine\ModulePath;

class ModulePathHelpers implements ModulePathHelpersInterface
{
    protected $modulePathManager;
    public function __construct(ModulePathManagerInterface $modulePathManager)
    {
        $this->modulePathManager = $modulePathManager;
    }

    public function getStringifiedModulePropagationCurrentPath($module)
    {
        $module_propagation_current_path = $this->modulePathManager->getPropagationCurrentPath();
        $module_propagation_current_path[] = $module;
        return $this->stringifyModulePath($module_propagation_current_path);
    }

    public function stringifyModulePath($modulepath)
    {
        return implode(POP_CONSTANT_MODULESTARTPATH_SEPARATOR, $modulepath);
    }

    public function recastModulePath($modulepath_as_string)
    {
        return explode(POP_CONSTANT_MODULESTARTPATH_SEPARATOR, $modulepath_as_string);
    }
}