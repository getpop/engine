<?php
namespace PoP\Engine\DirectiveResolvers;

use PoP\ComponentModel\FieldResolvers\AbstractFieldResolver;
use PoP\ComponentModel\FieldResolvers\FieldResolverInterface;
use PoP\ComponentModel\DirectiveResolvers\AbstractSchemaDirectiveResolver;

abstract class AbstractGlobalDirectiveResolver extends AbstractSchemaDirectiveResolver
{
    public static function getClassesToAttachTo(): array
    {
        // Be attached to all fieldResolvers
        return [
            AbstractFieldResolver::class,
        ];
    }

    public function isGlobal(FieldResolverInterface $fieldResolver): bool
    {
        return true;
    }
}
