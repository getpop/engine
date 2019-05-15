<?php

use PoP\Root\Container\ContainerBuilderFactory;

$containerBuilder = ContainerBuilderFactory::getInstance();

// ModuleFilters
$moduleFilterManager = $containerBuilder->get('\PoP\Engine\Contracts\ModuleFilterManager');
$moduleFilterManager->add([
    new \PoP\Engine\ModuleFilters\HeadModule(),
]);