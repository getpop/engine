<?php

declare(strict_types=1);

namespace PoP\Engine;

class Environment
{
    public const ADD_MANDATORY_CACHE_CONTROL_DIRECTIVE = 'ADD_MANDATORY_CACHE_CONTROL_DIRECTIVE';

    public static function disablePersistingDefinitionsOnEachRequest(): bool
    {
        return isset($_ENV['DISABLE_PERSISTING_DEFINITIONS_ON_EACH_REQUEST']) ? strtolower($_ENV['DISABLE_PERSISTING_DEFINITIONS_ON_EACH_REQUEST']) == "true" : false;
    }

    public static function disableGuzzleOperators(): bool
    {
        return isset($_ENV['DISABLE_GUZZLE_OPERATORS']) ? strtolower($_ENV['DISABLE_GUZZLE_OPERATORS']) == "true" : false;
    }

    public static function addMandatoryCacheControlDirective(): bool
    {
        return isset($_ENV[self::ADD_MANDATORY_CACHE_CONTROL_DIRECTIVE]) ? strtolower($_ENV[self::ADD_MANDATORY_CACHE_CONTROL_DIRECTIVE]) == "true" : true;
    }
}
