<?php
namespace PoP\Engine\Facades;

use PoP\Engine\Info\ApplicationInfoInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class ApplicationInfo
{
    public static function getInstance(): ApplicationInfoInterface
    {
        return ContainerBuilderFactory::getInstance()->get('application_info');
    }
}
