<?php
namespace PoP\Engine\Facades;

use PoP\Engine\Managers\InstanceManagerInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class InstanceManagerFacade
{
    public static function getInstance(): InstanceManagerInterface
    {
        return ContainerBuilderFactory::getInstance()->get('instance_manager');
    }
}
