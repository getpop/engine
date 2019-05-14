<?php
namespace PoP\Engine\ModelInstance;
use PoP\Translation\Contracts\TranslationAPIInterface;
use PoP\Hooks\Contracts\HooksAPIInterface;

class ModelInstance implements ModelInstanceInterface
{
    private $translationAPI;
    private $hooksAPI;

    public function __construct(TranslationAPIInterface $translationAPI, HooksAPIInterface $hooksAPI)
    {
        $this->translationAPI = $translationAPI;
        $this->hooksAPI = $hooksAPI;
    }

    public function getModelInstanceId(): string
    {
        // The string is too long. Use a hashing function to shorten it
        return md5(implode('-', $this->getModelInstanceComponents()));
    }

    protected function getModelInstanceComponents(): array
    {
        $components = array();

        // Add the version, because otherwise there may be PHP errors
        // happening from stale configuration that is not deleted, and still served, after a new version is deployed
        $components[] = $this->translationAPI->__('version:', 'engine').\popVersion();

        // Mix the information specific to the module, with that present in $vars
        return $this->hooksAPI->applyFilters(
            'PoP\Engine\ModelInstance\ModelInstance:components:result',
            array_merge(
                $components,
                $this->getModelInstanceComponentsFromVars()
            )
        );
    }

    protected function getModelInstanceComponentsFromVars(): array
    {
        $components = array();
        
        $vars = \PoP\Engine\Engine_Vars::getVars();

        // There will always be a nature. Add it.
        $nature = $vars['nature'];
        $route = $vars['route'];
        $components[] = $this->translationAPI->__('nature:', 'engine').$nature;
        $components[] = $this->translationAPI->__('route:', 'engine').$route;

        // Other properties
        if ($format = $vars['format']) {
            $components[] = $this->translationAPI->__('format:', 'engine').$format;
        }
        if ($target = $vars['target']) {
            $components[] = $this->translationAPI->__('target:', 'engine').$target;
        }
        if ($action = $vars['action']) {
            $components[] = $this->translationAPI->__('action:', 'engine').$action;
        }
        if ($config = $vars['config']) {
            $components[] = $this->translationAPI->__('config:', 'engine').$config;
        }
        if ($modulefilter = $vars['modulefilter']) {
            $components[] = $this->translationAPI->__('module filter:', 'engine').$modulefilter;

            if ($modulefilter == POP_MODULEFILTER_MODULEPATHS && ($modulepaths = $vars['modulepaths'])) {
                $paths = array();
                foreach ($modulepaths as $modulepath) {
                    $paths[] = \PoP\Engine\ModulePathManager_Utils::stringifyModulePath($modulepath);
                }
                
                $components[] = $this->translationAPI->__('module paths:', 'engine').implode(',', $paths);
            } elseif ($modulefilter == POP_MODULEFILTER_HEADMODULE && ($headmodule = $vars['headmodule'])) {
                $components[] = $this->translationAPI->__('head module:', 'engine').$headmodule;
            }
        }

        // Can the configuration change when doing a POST or GET?
        if ($this->hooksAPI->applyFilters(
            'PoP\Engine\ModelInstance\ModelInstance:componentsFromVars:postOrGetChange', 
            false
        )) {
            $components[] = $this->translationAPI->__('operation:', 'engine').(doingPost() ? 'post' : 'get');
        }
        if ($mangled = $vars['mangled']) {
            // By default it is mangled. To make it non-mangled, url must have param "mangled=none",
            // so only in these exceptional cases the identifier will add this parameter
            $components[] = $this->translationAPI->__('mangled:', 'engine').$mangled;
        }

        // Allow for plug-ins to add their own vars. Eg: URE source parameter
        return $this->hooksAPI->applyFilters(
            'PoP\Engine\ModelInstance\ModelInstance:componentsFromVars:result', 
            $components
        );
    }
}
