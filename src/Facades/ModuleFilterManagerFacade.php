<?php
namespace PoP\Engine\Facades;

use PoP\Engine\Managers\ModuleFilterManagerInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class ModuleFilterManagerFacade
{
    public static function getInstance(): ModuleFilterManagerInterface
    {
        return ContainerBuilderFactory::getInstance()->get('module_filter_manager');
    }
}
