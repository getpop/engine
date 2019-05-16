<?php
namespace PoP\Engine\Facades;

use PoP\Engine\ModulePath\ModulePathManagerInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class ModulePathManager
{
    public static function getInstance(): ModulePathManagerInterface
    {
        return ContainerBuilderFactory::getInstance()->get('module_path_manager');
    }
}
