<?php
namespace PoP\Engine\Facades;

use PoP\Engine\ModulePath\ModulePathHelpersInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class ModulePathHelpers
{
    public static function getInstance(): ModulePathHelpersInterface
    {
        return ContainerBuilderFactory::getInstance()->get('module_path_helpers');
    }
}