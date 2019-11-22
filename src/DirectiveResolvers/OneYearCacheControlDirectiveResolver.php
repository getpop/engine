<?php
namespace PoP\Engine\DirectiveResolvers;

use PoP\CacheControl\DirectiveResolvers\AbstractCacheControlDirectiveResolver;

class OneYearCacheControlDirectiveResolver extends AbstractCacheControlDirectiveResolver
{
    public static function getFieldNamesToApplyTo(): array
    {
        return [
            'id',
            // operators and helpers...
            'if',
            'not',
            'and',
            'or',
            'equals',
            'empty',
            'isNull',
            'sprintf',
            'concat',
            'echo',
            'divide',
            'arrayRandom',
            'arrayJoin',
            'arrayItem',
            'arraySearch',
            'arrayFill',
            'arrayValues',
            'arrayUnique',
            'arrayDiff',
            'arrayAddItem',
            'arrayAsQueryStr',
        ];
    }

    public function getMaxAge(): int
    {
        // One year = 315360000 seconds
        return 315360000;
    }
}
