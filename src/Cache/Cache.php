<?php
namespace PoP\Engine\Cache;

class Cache extends \PoP\ComponentModel\Cache\Cache
{
    protected function getCacheReplacements()
    {
        return [
            POP_CONSTANT_UNIQUE_ID => POP_CACHEPLACEHOLDER_UNIQUE_ID,
            POP_CONSTANT_CURRENTTIMESTAMP => POP_CACHEPLACEHOLDER_CURRENTTIMESTAMP,
            POP_CONSTANT_RAND => POP_CACHEPLACEHOLDER_RAND,
            POP_CONSTANT_TIME => POP_CACHEPLACEHOLDER_TIME,
        ];
    }
}
