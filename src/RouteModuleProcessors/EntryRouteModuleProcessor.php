<?php
namespace PoP\Engine\RouteModuleProcessors;

use PoP\ModuleRouting\AbstractEntryRouteModuleProcessor;

class EntryRouteModuleProcessor extends AbstractEntryRouteModuleProcessor
{
    public function getModulesVarsProperties()
    {
        $ret = array();

        $ret[] = [
            'module' => [PoP_Engine_Module_Processor_Elements::class, PoP_Engine_Module_Processor_Elements::MODULE_EMPTY],
        ];

        return $ret;
    }
}
