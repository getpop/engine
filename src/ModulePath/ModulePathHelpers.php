<?php
namespace PoP\Engine\ModulePath;
use \PoP\Engine\Modules\Constants;

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
        $moduleprocessor_manager = \PoP\Engine\ModuleProcessorManagerFactory::getInstance();
        return implode(
            POP_CONSTANT_MODULESTARTPATH_SEPARATOR, 
            array_map(
                function($module) use ($moduleprocessor_manager) {
                    return $moduleprocessor_manager->getProcessor($module)->getModuleFullName($module);
                },
                $modulepath
            )
        );
    }

    public function recastModulePath($modulepath_as_string)
    {
        return array_map(
            function($moduleFullName) {
                return explode(
                    Constants::SEPARATOR_MODULEFULLNAME, 
                    $moduleFullName
                );
            },
            explode(
                POP_CONSTANT_MODULESTARTPATH_SEPARATOR, 
                $modulepath_as_string
            )
        );
    }
}
