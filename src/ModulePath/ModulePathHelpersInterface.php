<?php
namespace PoP\Engine\ModulePath;

interface ModulePathHelpersInterface
{
    public function getStringifiedModulePropagationCurrentPath($module);
    public function stringifyModulePath($modulepath);
    public function recastModulePath($modulepath_as_string);
}
