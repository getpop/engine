<?php
namespace PoP\Engine\HookImplementations\ModuleFilters;

use PoP\Engine\Facades\ModulePathHelpers;
use PoP\Engine\Hooks\AbstractHookImplementation;

class ModulePaths extends AbstractHookImplementation
{
    protected $modulePathHelpers;
    public function __construct()
    {
        parent::__construct();
        $this->modulePathHelpers = ModulePathHelpers::getInstance();
        $this->hooksAPI->addFilter(
            'PoP\Engine\ModelInstance\ModelInstance:componentsFromVars:result',
            [$this, 'maybeAddComponent']
        );
        $this->hooksAPI->addAction(
            '\PoP\Engine\Engine_Vars:addVars',
            [$this, 'addVars'],
            10,
            1
        );
    }
    public function addVars($vars_in_array)
    {
        $vars = &$vars_in_array[0];
        if ($vars['modulefilter'] == \PoP\Engine\ModuleFilters\ModulePaths::MODULEFILTER_MODULEPATHS) {
            $vars['modulepaths'] = \PoP\Engine\Engine_Vars::getModulePaths();
        }
    }
    public function maybeAddComponent($components)
    {
        $vars = \PoP\Engine\Engine_Vars::getVars();
        if ($vars['modulefilter'] == \PoP\Engine\ModuleFilters\ModulePaths::MODULEFILTER_MODULEPATHS) {
            
            if ($modulepaths = $vars['modulepaths']) {
                $paths = array();
                foreach ($modulepaths as $modulepath) {
                    $paths[] = $this->modulePathHelpers->stringifyModulePath($modulepath);
                }
                $components[] = $this->translationAPI->__('module paths:', 'engine') . implode(',', $paths);
            }
        }

        return $components;
    }
}
