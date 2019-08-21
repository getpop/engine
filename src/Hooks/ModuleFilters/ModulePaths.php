<?php
namespace PoP\Engine\Hooks\ModuleFilters;

use PoP\ComponentModel\Facades\ModulePathHelpersFacade;
use PoP\Engine\Hooks\AbstractHookImplementation;

class ModulePaths extends AbstractHookImplementation
{
    protected $modulePathHelpers;
    public function __construct()
    {
        parent::__construct();
        $this->modulePathHelpers = ModulePathHelpersFacade::getInstance();
        $this->hooksAPI->addFilter(
            'PoP\ComponentModel\ModelInstance\ModelInstance:componentsFromVars:result',
            [$this, 'maybeAddComponent']
        );
        $this->hooksAPI->addAction(
            '\PoP\ComponentModel\Engine_Vars:addVars',
            [$this, 'addVars'],
            10,
            1
        );
    }
    public function addVars($vars_in_array)
    {
        $vars = &$vars_in_array[0];
        if ($vars['modulefilter'] == \PoP\Engine\ModuleFilters\ModulePaths::NAME) {
            $vars['modulepaths'] = \PoP\ComponentModel\Engine_Vars::getModulePaths();
        }
    }
    public function maybeAddComponent($components)
    {
        $vars = \PoP\ComponentModel\Engine_Vars::getVars();
        if ($vars['modulefilter'] == \PoP\Engine\ModuleFilters\ModulePaths::NAME) {
            if ($modulepaths = $vars['modulepaths']) {
                $paths = array_map(
                    function($modulepath) {
                        return $this->modulePathHelpers->stringifyModulePath($modulepath);
                    },
                    $modulepaths
                );
                $components[] = $this->translationAPI->__('module paths:', 'engine').implode(',', $paths);
            }
        }

        return $components;
    }
}
