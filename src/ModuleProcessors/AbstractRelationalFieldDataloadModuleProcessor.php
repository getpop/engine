<?php
namespace PoP\Engine\ModuleProcessors;

use PoP\ComponentModel\ModuleProcessors\AbstractDataloadModuleProcessor;

abstract class AbstractRelationalFieldDataloadModuleProcessor extends AbstractDataloadModuleProcessor
{
    protected function getInnerSubmodules(array $module): array
    {
        $ret = parent::getInnerSubmodules($module);
        // The fields to retrieve are passed through module atts, so simply transfer all module atts down the line
        $ret[] = [RelationalFieldQueryDataModuleProcessor::class, RelationalFieldQueryDataModuleProcessor::MODULE_LAYOUT_RELATIONALFIELDS, $module[2]];
        return $ret;
    }

    public function getFormat(array $module): ?string
    {
        return POP_FORMAT_FIELDS;
    }
}
