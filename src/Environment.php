<?php
namespace PoP\Engine;

class Environment
{
    public static function disablePersistingDefinitionsOnEachRequest(): bool
    {
        return isset($_ENV['DISABLE_PERSISTING_DEFINITIONS_ON_EACH_REQUEST']) ? strtolower($_ENV['DISABLE_PERSISTING_DEFINITIONS_ON_EACH_REQUEST']) == "true" : false;
    }

    public static function disableGuzzleOperators(): bool
    {
        return isset($_ENV['DISABLE_GUZZLE_OPERATORS']) ? strtolower($_ENV['DISABLE_GUZZLE_OPERATORS']) == "true" : false;
    }
}
