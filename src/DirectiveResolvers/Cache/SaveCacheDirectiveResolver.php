<?php
namespace PoP\Engine\DirectiveResolvers\Cache;

use PoP\Engine\Cache\CacheTypes;
use PoP\ComponentModel\Feedback\Tokens;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\Facades\Cache\PersistentCacheFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\Facades\Schema\FieldQueryInterpreterFacade;
use PoP\ComponentModel\DirectiveResolvers\AbstractGlobalDirectiveResolver;

/**
 * Save the field value into the cache. This directive is executed after `@resolveAndMerge`,
 * and it works together with "@loadCache" which is executed before `@resolveAndMerge`.
 * If @loadCache finds there's a cached value already, then the idsDataFields for directives
 * @resolveAndMerge and this @saveCache (called @cache) will be removed, so they have nothing to do
 */
class SaveCacheDirectiveResolver extends AbstractGlobalDirectiveResolver
{
    use CacheDirectiveResolverTrait;

    /**
     * It's called "cache" instead of "saveCache" because it's more user-friendly,
     * and because "cache" involves both "loadCache" and "saveCache", where "loadCache"
     * is added as a mandatory directive on directive
     */
    const DIRECTIVE_NAME = 'cache';
    public static function getDirectiveName(): string
    {
        return self::DIRECTIVE_NAME;
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
        $translationAPI = TranslationAPIFacade::getInstance();
        foreach ($idsDataFields as $id => $dataFields) {
            foreach ($dataFields['direct'] as $field) {
                $cacheID = $this->getCacheID($typeResolver, $id, $field);
                $fieldOutputKey = $fieldQueryInterpreter->getFieldOutputKey($field);
                if (!array_key_exists($fieldOutputKey, $dbItems[(string)$id])) {
                    $dbWarnings[(string)$id][] = [
                        Tokens::PATH => [$this->directive],
                        Tokens::MESSAGE => sprintf(
                            $translationAPI->__('Property \'%s\' doesn\'t exist in object with ID \'%s\', so it can\'t be cached'),
                            $fieldOutputKey,
                            $id
                        ),
                    ];
                    continue;
                }
                $persistentCache->storeCache(
                    $cacheID,
                    CacheTypes::CACHE_DIRECTIVE,
                    $dbItems[(string)$id][$fieldOutputKey],
                    $this->directiveArgsForSchema['time']
                );
            }
        }
    }
    public function getSchemaDirectiveDescription(TypeResolverInterface $typeResolver): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('Cache the field value, and retrive from the cache if available', 'engine');
    }
    public function getSchemaDirectiveArgs(TypeResolverInterface $typeResolver): array
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return [
            [
                SchemaDefinition::ARGNAME_NAME => 'time',
                SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_INT,
                SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Amount of time, in seconds, that the cache is valid. If not defining this value, the cache has no expiry date', 'engine'),
            ],
        ];
    }
}
