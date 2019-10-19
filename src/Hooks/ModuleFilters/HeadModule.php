<?php
namespace PoP\Engine\Hooks\ModuleFilters;

use PoP\Engine\ModuleFilters\Constants;
use PoP\Hooks\Contracts\HooksAPIInterface;
use PoP\ComponentModel\Modules\ModuleUtils;
use PoP\ComponentModel\Hooks\AbstractHookSet;
use PoP\ComponentModel\ModelInstance\ModelInstance;
use PoP\Translation\Contracts\TranslationAPIInterface;

class HeadModule extends AbstractHookSet
{
    public function __construct(
        HooksAPIInterface $hooksAPI,
        TranslationAPIInterface $translationAPI
    ) {
        parent::__construct($hooksAPI, $translationAPI);

        $this->hooksAPI->addFilter(
            ModelInstance::HOOK_COMPONENTSFROMVARS_RESULT,
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
        if ($vars['modulefilter'] == \PoP\Engine\ModuleFilters\HeadModule::NAME) {
            if ($headmodule = $_REQUEST[Constants::URLPARAM_HEADMODULE]) {
                $vars['headmodule'] = ModuleUtils::getModuleFromOutputName($headmodule);
            }
        }
    }
    public function maybeAddComponent($components)
    {
        $vars = \PoP\ComponentModel\Engine_Vars::getVars();
        if ($vars['modulefilter'] == \PoP\Engine\ModuleFilters\HeadModule::NAME) {
            if ($headmodule = $vars['headmodule']) {
                $components[] = $this->translationAPI->__('head module:', 'engine').ModuleUtils::getModuleFullName($headmodule);
            }
        }

        return $components;
    }
}
