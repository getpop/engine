<?php
namespace PoP\Engine\Hooks;

use PoP\Translation\Facades\TranslationAPI;

class AbstractHookImplementation extends \PoP\Hooks\Hooks\AbstractHookImplementation
{
    protected $translationAPI;
    public function __construct()
    {
        parent::__construct();
        $this->translationAPI = TranslationAPI::getInstance();
    }
}
