<?php

declare(strict_types=1);

namespace PoP\Engine\Hooks\ModuleFilters;

use PoP\Engine\Hooks\AbstractHookSet;
use PoP\ComponentModel\Facades\ModulePath\ModulePathHelpersFacade;
use PoP\ComponentModel\ModulePath\ModulePathUtils;
use PoP\ComponentModel\State\ApplicationState;

class ModulePaths extends AbstractHookSet
{
    protected $modulePathHelpers;

    protected function init()
    {
        $this->modulePathHelpers = ModulePathHelpersFacade::getInstance();
        $this->hooksAPI->addFilter(
            'PoP\ComponentModel\ModelInstance\ModelInstance:componentsFromVars:result',
            [$this, 'maybeAddComponent']
        );
        $this->hooksAPI->addAction(
            'ApplicationState:addVars',
            [$this, 'addVars'],
            10,
            1
        );
    }
    public function addVars($vars_in_array)
    {
        $vars = &$vars_in_array[0];
        if ($vars['modulefilter'] == \PoP\ComponentModel\ModuleFilters\ModulePaths::NAME) {
            $vars['modulepaths'] = ModulePathUtils::getModulePaths();
        }
    }
    public function maybeAddComponent($components)
    {
        $vars = ApplicationState::getVars();
        if ($vars['modulefilter'] == \PoP\ComponentModel\ModuleFilters\ModulePaths::NAME) {
            if ($modulepaths = $vars['modulepaths']) {
                $paths = array_map(
                    function ($modulepath) {
                        return $this->modulePathHelpers->stringifyModulePath($modulepath);
                    },
                    $modulepaths
                );
                $components[] = $this->translationAPI->__('module paths:', 'engine') . implode(',', $paths);
            }
        }

        return $components;
    }
}
