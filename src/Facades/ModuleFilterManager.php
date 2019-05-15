<?php
namespace PoP\Engine\Facades;

use PoP\Engine\ModuleFilters\ModuleFilterManagerInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class ModuleFilterManager
{
    public static function getInstance(): ModuleFilterManagerInterface
    {
        return ContainerBuilderFactory::getInstance()->get('\PoP\Engine\Contracts\ModuleFilterManager');
    }
}
