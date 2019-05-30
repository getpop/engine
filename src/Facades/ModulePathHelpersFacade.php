<?php
namespace PoP\Engine\Facades;

use PoP\Engine\Managers\ModulePathHelpersInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class ModulePathHelpersFacade
{
    public static function getInstance(): ModulePathHelpersInterface
    {
        return ContainerBuilderFactory::getInstance()->get('module_path_helpers');
    }
}
