<?php

use PoP\Root\Container\ContainerBuilderFactory;

$containerBuilder = ContainerBuilderFactory::getInstance();

// Add ModuleFilters to the ModuleFilterManager
$containerBuilder->get('module_filter_manager')->add([
    new \PoP\Engine\ModuleFilters\HeadModule(),
    new \PoP\Engine\ModuleFilters\ModulePaths(
        $containerBuilder->get('module_path_manager')
    ),
    new \PoP\Engine\ModuleFilters\Lazy(),
    new \PoP\Engine\ModuleFilters\MainContentModule(),
]);