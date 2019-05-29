<?php
namespace PoP\Engine\Component;

use HaydenPierce\ClassFinder\ClassFinder;

trait InstantiateNamespaceClassesTrait
{
    /**
     * Instantiate immediately-required classes
     *
     * @param array $namespaces
     * @return void
     */
    public static function instantiateNamespaceClasses(array $namespaces)
    {
        foreach ($namespaces as $namespace) {
            $classes = ClassFinder::getClassesInNamespace($namespace, ClassFinder::RECURSIVE_MODE);
            foreach ($classes as $class) {
                new $class();
            }
        }
    }
}
