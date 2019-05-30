<?php
namespace PoP\Engine\Hooks;

use PoP\Hooks\Facades\HooksAPI;
use PoP\Translation\Facades\TranslationAPI;

class AbstractHookImplementation
{
    protected $hooksAPI;
    protected $translationAPI;
    public function __construct()
    {
        $this->hooksAPI = HooksAPI::getInstance();
        $this->translationAPI = TranslationAPI::getInstance();
    }
}
