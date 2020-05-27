<?php

declare(strict_types=1);

namespace PoP\Engine;

use PoP\ComponentModel\ComponentConfiguration\ComponentConfigurationTrait;

class ComponentConfiguration
{
    use ComponentConfigurationTrait;

    private static $addMandatoryCacheControlDirective;

    public static function addMandatoryCacheControlDirective(): bool
    {
        // Define properties
        $envVariable = Environment::ADD_MANDATORY_CACHE_CONTROL_DIRECTIVE;
        $selfProperty = &self::$addMandatoryCacheControlDirective;
        $callback = [Environment::class, 'addMandatoryCacheControlDirective'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $callback,
            false
        );
        return $selfProperty;
    }
}
