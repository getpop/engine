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
    }
    public function maybeAddComponent($components)
    {
        $vars = \PoP\Engine\Engine_Vars::getVars();
        if ($vars['modulefilter'] == POP_MODULEFILTER_HEADMODULE) {
            if ($headmodule = $vars['headmodule']) {
                $components[] = $this->translationAPI->__('head module:', 'engine') . $headmodule;
            }
        }

        return $components;
    }
}
