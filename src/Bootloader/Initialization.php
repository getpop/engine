<?php
namespace PoP\Engine\Bootloader;

class Initialization
{
    public static function init()
    {
        InstantiateNamespaceClasses::init();
    }
}
