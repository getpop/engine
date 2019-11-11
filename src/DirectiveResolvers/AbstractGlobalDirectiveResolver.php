<?php
namespace PoP\Engine\DirectiveResolvers;

use PoP\ComponentModel\DirectiveResolvers\AbstractSchemaDirectiveResolver;

abstract class AbstractGlobalDirectiveResolver extends AbstractSchemaDirectiveResolver
{
    use GlobalDirectiveResolverTrait;
}
