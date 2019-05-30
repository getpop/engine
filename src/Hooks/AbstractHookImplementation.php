<?php
namespace PoP\Engine\Hooks;

use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\Translation\Facades\TranslationAPIFacade;

class AbstractHookImplementation
{
    protected $hooksAPI;
    protected $translationAPI;
    public function __construct()
    {
        $this->hooksAPI = HooksAPIFacade::getInstance();
        $this->translationAPI = TranslationAPIFacade::getInstance();
    }
}
