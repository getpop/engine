<?php
namespace PoP\Engine\ModuleFilters;

class Lazy extends AbstractModuleFilter
{
    const NAME = 'lazy';

    public function getName()
    {
        return self::NAME;
    }

    public function excludeModule(array $module, array &$props)
    {
        // Exclude if it is not lazy
        $moduleprocessor_manager = \PoP\Engine\ModuleProcessorManagerFactory::getInstance();
        $processor = $moduleprocessor_manager->getProcessor($module);
        return !$processor->isLazyload($module, $props);
    }
}
