<?php
namespace PoP\Engine\Hooks\Misc;

use PoP\Engine\Environment;
use PoP\Engine\Hooks\AbstractHookSet;
use PoP\Definitions\Facades\DefinitionManagerFacade;

class DefinitionPersistenceHookSet extends AbstractHookSet
{
    protected function init()
    {
        $this->hooksAPI->addAction(
            'popcms:shutdown',
            array($this, 'maybePersist')
        );
    }
    public function maybePersist()
    {
        if (!Environment::disablePersistingDefinitionsOnEachRequest()) {
            DefinitionManagerFacade::getInstance()->maybeStoreDefinitionsPersistently();
        }
    }
}