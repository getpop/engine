<?php
namespace PoP\Engine\Component;

use PoP\Engine\Bootloader\InstantiateNamespaceClasses;

trait InstantiateNamespaceClassesTrait
{
    public static function instantiateNamespaceClasses(array $namespaces)
    {
        InstantiateNamespaceClasses::addNamespaces($namespaces);
    }
}
