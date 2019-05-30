<?php
namespace PoP\Engine\HookImplementations\ModuleFilters;

use PoP\Engine\ModuleUtils;
use PoP\Engine\ModuleFilter\Constants;
use PoP\Engine\ModelInstance\ModelInstance;
use PoP\Engine\Hooks\AbstractHookImplementation;

class HeadModule extends AbstractHookImplementation
{
    public function __construct()
    {
        parent::__construct();
        $this->hooksAPI->addFilter(
            ModelInstance::HOOK_COMPONENTSFROMVARS_RESULT,
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
        if ($vars['modulefilter'] == \PoP\Engine\ModuleFilter\Implementations\HeadModule::NAME) {
            if ($headmodule = $_REQUEST[Constants::URLPARAM_HEADMODULE]) {
                $vars['headmodule'] = ModuleUtils::getModuleFromOutputName($headmodule);
            }
        }
    }
    public function maybeAddComponent($components)
    {
        $vars = \PoP\Engine\Engine_Vars::getVars();
        if ($vars['modulefilter'] == \PoP\Engine\ModuleFilter\Implementations\HeadModule::NAME) {
            if ($headmodule = $vars['headmodule']) {
                $components[] = $this->translationAPI->__('head module:', 'engine').ModuleUtils::getModuleFullName($headmodule);
            }
        }

        return $components;
    }
}
