<?php

use PoP\Root\Container\ContainerBuilderFactory;

$containerBuilder = ContainerBuilderFactory::getInstance();

// Add ModuleFilters to the ModuleFilterManager
$containerBuilder->get('module_filter_manager')->add([
    $containerBuilder->get('module_filters.head_module'),
    $containerBuilder->get('module_filters.module_paths'),
    $containerBuilder->get('module_filters.lazy'),
    $containerBuilder->get('module_filters.main_content_module'),
]);