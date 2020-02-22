<?php
namespace PoP\Engine\Hooks;

abstract class AbstractCMSHookSet extends AbstractHookSet
{
    /**
     * Initialize the hooks when the CMS initializes
     *
     * @return void
     */
    protected function init()
    {
        $this->hooksAPI->addAction(
            'popcms:init',
            [$this, 'cmsInit']
        );
    }
    abstract public function cmsInit(): void;
}
