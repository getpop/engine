<?php
namespace PoP\Engine\Hooks;

use PoP\Hooks\Contracts\HooksAPIInterface;
use PoP\Translation\Contracts\TranslationAPIInterface;

abstract class AbstractHookSet
{
    protected $hooksAPI;
    protected $translationAPI;
    public function __construct(
        HooksAPIInterface $hooksAPI,
        TranslationAPIInterface $translationAPI
    ) {
        $this->hooksAPI = $hooksAPI;
        $this->translationAPI = $translationAPI;

        // Initialize the hooks
        $this->init();
    }
    /**
     * Initialize the hooks
     *
     * @return void
     */
    protected abstract function init();
}
