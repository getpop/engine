<?php
namespace PoP\Engine\DirectiveResolvers\Cache;

use PoP\Engine\Cache\CacheTypes;
use PoP\ComponentModel\Engine_Vars;
use PoP\FieldQuery\FieldQueryInterpreter;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\Facades\Schema\FieldQueryInterpreterFacade;

/**
 * Common functionality between LoadCache and SaveCache directive resolver classes
 */
trait CacheDirectiveResolverTrait
{
    /**
     * Caching can be executed only once
     *
     * @return boolean
     */
    public function canExecuteMultipleTimesInField(): bool
    {
        return false;
    }

    /**
     * Namespaced/normal schemas must be stored under different keys or it produces
     * an error when switching from one to the other (eg: doing /?use_namespace=1)
     *
     * @return string
     */
    protected function getCacheType(): string
    {
        $vars = Engine_Vars::getVars();
        return $vars['namespace-types-and-interfaces'] ?
            CacheTypes::NAMESPACED_CACHE_DIRECTIVE :
            CacheTypes::CACHE_DIRECTIVE;
    }

    /**
     * Create a unique ID under which to store the cache, based on the type, ID and field (without the alias)
     *
     * @param TypeResolverInterface $typeResolver
     * @param [type] $id
     * @param string $field
     * @return string
     */
    protected function getCacheID(TypeResolverInterface $typeResolver, $id, string $field): string
    {
        // Remove the alias from the field
        $fieldQueryInterpreter = FieldQueryInterpreterFacade::getInstance();
        if ($fieldAliasPositionSpan = $fieldQueryInterpreter->getFieldAliasPositionSpanInField($field)) {
            $aliasPos = $fieldAliasPositionSpan[FieldQueryInterpreter::ALIAS_POSITION_KEY];
            $aliasLength = $fieldAliasPositionSpan[FieldQueryInterpreter::ALIAS_LENGTH_KEY];
            $noAliasField = substr($field, 0, $aliasPos).substr($field, $aliasPos+$aliasLength);
        } else {
            $noAliasField = $field;
        }
        $components = [
            $typeResolver->getNamespacedTypeName(),
            $id,
            $noAliasField
        ];
        $cacheID = implode('|', $components);
        /**
         * Hash this key, because the $field may contain reserved characters, such as "()" for the field args:
         * PHP Fatal error:  Uncaught Symfony\Component\Cache\Exception\InvalidArgumentException: Cache key "cache-directive.Root|root|echo(hola)<cache>" contains reserved characters "{}()/\@:". in .../vendor/symfony/cache/CacheItem.php:177
         */
        return hash('md5', $cacheID);
    }
}
