<?php
namespace PoP\Engine\Facades;

use PoP\Engine\ModuleFilter\ModuleFilterManagerInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class ModuleFilterManager
{
    public static function getInstance(): ModuleFilterManagerInterface
    {
        return ContainerBuilderFactory::getInstance()->get('module_filter_manager');
    }
}
