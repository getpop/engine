<?php
namespace PoP\Engine\ModuleProcessors;

use PoP\Engine\ModuleProcessors\AbstractRelationalFieldDataloadModuleProcessor;
use PoP\Engine\TypeResolvers\RootTypeResolver;
use PoP\Engine\ObjectModels\Root;

class RootRelationalFieldDataloadModuleProcessor extends AbstractRelationalFieldDataloadModuleProcessor
{
    public const MODULE_DATALOAD_RELATIONALFIELDS_ROOT = 'dataload-relationalfields-root';

    public function getModulesToProcess(): array
    {
        return array(
            [self::class, self::MODULE_DATALOAD_RELATIONALFIELDS_ROOT],
        );
    }

    public function getDBObjectIDOrIDs(array $module, array &$props, &$data_properties)
    {
        switch ($module[1]) {
            case self::MODULE_DATALOAD_RELATIONALFIELDS_ROOT:
                return Root::ID;
        }
        return parent::getDBObjectIDOrIDs($module, $props, $data_properties);
    }

    public function getTypeResolverClass(array $module): ?string
    {
        switch ($module[1]) {
            case self::MODULE_DATALOAD_RELATIONALFIELDS_ROOT:
                return RootTypeResolver::class;
        }

        return parent::getTypeResolverClass($module);
    }
}



