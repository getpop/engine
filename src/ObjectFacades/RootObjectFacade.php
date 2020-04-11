<?php
namespace PoP\Engine\ObjectFacades;

use PoP\Engine\ObjectModels\Root;
use PoP\Root\Container\ContainerBuilderFactory;

class RootObjectFacade
{
    public static function getInstance(): Root
    {
        $containerBuilderFactory = ContainerBuilderFactory::getInstance();
        return $containerBuilderFactory->get('root_object');
    }
}
