<?php
namespace PoP\Engine\Bootloader;

use HaydenPierce\ClassFinder\ClassFinder;

class InstantiateNamespaceClasses
{
    private static $namespaces = [];
    public static function addNamespaces($namespaces)
    {
        self::$namespaces = array_unique(array_merge(
            self::$namespaces,
            $namespaces
        ));
    }
    public static function init()
    {
        foreach (self::$namespaces as $namespace) {
            $classes = ClassFinder::getClassesInNamespace($namespace, ClassFinder::RECURSIVE_MODE);
            foreach ($classes as $class) {
                new $class();
            }
        }
    }
}
