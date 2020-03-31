<?php
namespace PoP\Engine\DirectiveResolvers\Cache;

use PoP\Engine\Cache\CacheTypes;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\PipelinePositions;
use PoP\ComponentModel\Facades\Cache\PersistentCacheFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\Facades\Schema\FieldQueryInterpreterFacade;
use PoP\ComponentModel\DirectiveResolvers\AbstractGlobalDirectiveResolver;
use PoP\ComponentModel\DirectiveResolvers\RemoveIDsDataFieldsDirectiveResolverTrait;

/**
 * Load the field value from the cache. This directive is executed before `@resolveAndMerge`,
 * and it works together with "@saveCache" (called @cache) which is executed after `@resolveAndMerge`.
 * If @loadCache finds there's a cached value already, then the idsDataFields for directives
 * @resolveAndMerge and @saveCache will be removed, so they have nothing to do
 */
class LoadCacheDirectiveResolver extends AbstractGlobalDirectiveResolver
{
    use CacheDirectiveResolverTrait;
    use RemoveIDsDataFieldsDirectiveResolverTrait;

    const DIRECTIVE_NAME = 'loadCache';
    public static function getDirectiveName(): string
    {
        return self::DIRECTIVE_NAME;
    }

    /**
     * Place it after the validation and before it's added to $dbItems in the resolveAndMerge directive
     *
     * @return void
     */
    public function getPipelinePosition(): string
    {
        return PipelinePositions::AFTER_VALIDATE_BEFORE_RESOLVE;
    }

    /**
     * Save all the field values into the cache
     *
     * @param TypeResolverInterface $typeResolver
     * @param array $idsDataFields
     * @param array $succeedingPipelineIDsDataFields
     * @param array $resultIDItems
     * @param array $unionDBKeyIDs
     * @param array $dbItems
     * @param array $previousDBItems
     * @param array $variables
     * @param array $messages
     * @param array $dbErrors
     * @param array $dbWarnings
     * @param array $dbDeprecations
     * @param array $schemaErrors
     * @param array $schemaWarnings
     * @param array $schemaDeprecations
     * @return void
     */
    public function resolveDirective(TypeResolverInterface $typeResolver, array &$idsDataFields, array &$succeedingPipelineIDsDataFields, array &$resultIDItems, array &$unionDBKeyIDs, array &$dbItems, array &$previousDBItems, array &$variables, array &$messages, array &$dbErrors, array &$dbWarnings, array &$dbDeprecations, array &$schemaErrors, array &$schemaWarnings, array &$schemaDeprecations)
    {
        $persistentCache = PersistentCacheFacade::getInstance();
        $fieldQueryInterpreter = FieldQueryInterpreterFacade::getInstance();
        $idsDataFieldsToRemove = [];
        foreach ($idsDataFields as $id => $dataFields) {
            foreach ($dataFields['direct'] as $field) {
                $cacheID = $this->getCacheID($typeResolver, $id, $field);
                $fieldOutputKey = $fieldQueryInterpreter->getFieldOutputKey($field);
                if ($persistentCache->hasCache($cacheID, CacheTypes::CACHE_DIRECTIVE)) {
                    $dbItems[(string)$id][$fieldOutputKey] = $persistentCache->getCache($cacheID, CacheTypes::CACHE_DIRECTIVE);
                    $idsDataFieldsToRemove[(string)$id]['direct'][] = $field;
                }
            }
        }
        /**
         * Remove from directives @resolveAndMerge and @cache, nothing for them to do
         */
        if ($idsDataFieldsToRemove) {
            $this->removeIDsDataFields($idsDataFieldsToRemove, $succeedingPipelineIDsDataFields);
        }
    }
    public function getSchemaDirectiveDescription(TypeResolverInterface $typeResolver): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('Load the cached value for a field', 'engine');
    }
}
