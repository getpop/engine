<?php
namespace PoP\Engine\Configuration;

class Environment
{
    public static function disablePersistingDefinitionsOnEachRequest()
    {
        return isset($_ENV['DISABLE_PERSISTING_DEFINITIONS_ON_EACH_REQUEST']) ? strtolower($_ENV['DISABLE_PERSISTING_DEFINITIONS_ON_EACH_REQUEST']) == "true" : false;
    }
}

