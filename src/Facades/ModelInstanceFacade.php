<?php
namespace PoP\Engine\Facades;

use PoP\Engine\ModelInstance\ModelInstanceInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class ModelInstanceFacade
{
    public static function getInstance(): ModelInstanceInterface
    {
        return ContainerBuilderFactory::getInstance()->get('model_instance');
    }
}
