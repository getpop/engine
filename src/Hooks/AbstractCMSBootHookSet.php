<?php
namespace PoP\Engine\Hooks;

abstract class AbstractCMSBootHookSet extends AbstractHookSet
{
    /**
     * Initialize the hooks when the CMS initializes
     *
     * @return void
     */
    protected function init()
    {
        $this->hooksAPI->addAction(
            'popcms:boot',
            [$this, 'cmsBoot'],
            $this->getPriority()
        );
    }
    protected function getPriority(): int
    {
        return 10;
    }
    abstract public function cmsBoot(): void;
}