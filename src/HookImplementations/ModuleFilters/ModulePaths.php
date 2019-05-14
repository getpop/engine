<?php
namespace PoP\Engine\HookImplementations\ModuleFilters;

use PoP\Engine\Hooks\AbstractHookImplementation;

class ModulePaths extends AbstractHookImplementation
{
    public function __construct()
    {
        parent::__construct();
        $this->hooksAPI->addFilter(
            'PoP\Engine\ModelInstance\ModelInstance:componentsFromVars:result',
            [$this, 'maybeAddComponent']
        );
    }
    public function maybeAddComponent($components)
    {
        $vars = \PoP\Engine\Engine_Vars::getVars();
        if ($vars['modulefilter'] == POP_MODULEFILTER_MODULEPATHS) {
            
            if ($modulepaths = $vars['modulepaths']) {
                $paths = array();
                foreach ($modulepaths as $modulepath) {
                    $paths[] = \PoP\Engine\ModulePathManager_Utils::stringifyModulePath($modulepath);
                }
                $components[] = $this->translationAPI->__('module paths:', 'engine') . implode(',', $paths);
            }
        }

        return $components;
    }
}
