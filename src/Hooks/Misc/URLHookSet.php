<?php
namespace PoP\Engine\Hooks\Misc;

use PoP\Engine\Hooks\AbstractHookSet;
use PoP\Engine\ModuleFilters\HeadModule;

class URLHookSet extends AbstractHookSet
{
    protected function init()
    {
        $this->hooksAPI->addFilter(
            '\PoP\ComponentModel\Utils:current_url:remove_params',
            [$this, 'getParamsToRemoveFromURL']
        );
    }
    public function getParamsToRemoveFromURL($params)
    {
        $params[] = HeadModule::URLPARAM_HEADMODULE;
        return $params;
    }
}
