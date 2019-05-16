<?php
namespace PoP\Engine\ModuleFilters;

class Lazy extends AbstractModuleFilter
{
    const MODULEFILTER_LAZY = 'lazy';

    public function getName()
    {
        return self::MODULEFILTER_LAZY;
    }

    public function excludeModule($module, &$props)
    {
        // Exclude if it is not lazy
        $moduleprocessor_manager = \PoP\Engine\ModuleProcessorManagerFactory::getInstance();
        $processor = $moduleprocessor_manager->getProcessor($module);
        return !$processor->isLazyload($module, $props);
    }
}
