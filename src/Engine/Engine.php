<?php
namespace PoP\Engine\Engine;

use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\LooseContracts\Facades\Contracts\LooseContractManagerFacade;

class Engine extends \PoP\ComponentModel\Engine\Engine
{
    public function generateData()
    {
        // Check if there are hooks that must be implemented by the CMS, that have not been done so.
        // Check here, since we can't rely on addAction('popcms:init') to check, since we don't know if it was implemented!
        $looseContractManager = LooseContractManagerFacade::getInstance();
        $translationAPI = TranslationAPIFacade::getInstance();
        if ($notImplementedHooks = $looseContractManager->getNotImplementedRequiredHooks()) {
            throw new Exception(
                sprintf(
                    $translationAPI->__('The following hooks have not been implemented by the CMS: "%s". Hence, we can\'t continue.'),
                    implode($translationAPI->__('", "'), $notImplementedHooks)
                )
            );
        }
        if ($notImplementedNames = $looseContractManager->getNotImplementedRequiredNames()) {
            throw new Exception(
                sprintf(
                    $translationAPI->__('The following names have not been implemented by the CMS: "%s". Hence, we can\'t continue.'),
                    implode($translationAPI->__('", "'), $notImplementedNames)
                )
            );
        }

        parent::generateDate();
    }
}
