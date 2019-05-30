<?php
namespace PoP\Engine\Hook\Implementations\ModuleFilters;

use PoP\Engine\Facades\ModulePathHelpers;
use PoP\Engine\Hook\AbstractHookImplementation;

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
        if ($vars['modulefilter'] == \PoP\Engine\ModuleFilter\Implementations\ModulePaths::NAME) {
            $vars['modulepaths'] = \PoP\Engine\Engine_Vars::getModulePaths();
        }
    }
    public function maybeAddComponent($components)
    {
        $vars = \PoP\Engine\Engine_Vars::getVars();
        if ($vars['modulefilter'] == \PoP\Engine\ModuleFilter\Implementations\ModulePaths::NAME) {
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
