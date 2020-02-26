<?php
namespace PoP\Engine\DirectiveResolvers;

use PoP\ComponentModel\DirectiveResolvers\GlobalDirectiveResolverTrait;
use PoP\Engine\DirectiveResolvers\AbstractUseDefaultValueIfNullDirectiveResolver;

class UseDefaultValueIfNullDirectiveResolver extends AbstractUseDefaultValueIfNullDirectiveResolver
{
    use GlobalDirectiveResolverTrait;

    const DIRECTIVE_NAME = 'default';
    public static function getDirectiveName(): string {
        return self::DIRECTIVE_NAME;
    }
}
