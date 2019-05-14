<?php
namespace PoP\Engine\Facades;

use PoP\Engine\ModelInstance\ModelInstanceInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class ModelInstance
{
    public static function getInstance(): ModelInstanceInterface
    {
        return ContainerBuilderFactory::getInstance()->get('\PoP\Engine\Contracts\ModelInstance');
    }
}
