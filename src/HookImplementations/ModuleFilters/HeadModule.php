<?php
namespace PoP\Engine\HookImplementations\ModuleFilters;

use PoP\Engine\Hooks\AbstractHookImplementation;

class HeadModule extends AbstractHookImplementation
{
    public function __construct()
    {
        parent::__construct();
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
        if ($vars['modulefilter'] == \PoP\Engine\ModuleFilters\HeadModule::MODULEFILTER_HEADMODULE) {
            $vars['headmodule'] = $_REQUEST[GD_URLPARAM_HEADMODULE];
        }
    }
    public function maybeAddComponent($components)
    {
        $vars = \PoP\Engine\Engine_Vars::getVars();
        if ($vars['modulefilter'] == \PoP\Engine\ModuleFilters\HeadModule::MODULEFILTER_HEADMODULE) {
            if ($headmodule = $vars['headmodule']) {
                $components[] = $this->translationAPI->__('head module:', 'engine') . $headmodule;
            }
        }

        return $components;
    }
}
