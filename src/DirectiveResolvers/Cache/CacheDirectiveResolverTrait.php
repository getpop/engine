<?php
namespace PoP\Engine\DirectiveResolvers\Cache;

use PoP\ComponentModel\Facades\Schema\FieldQueryInterpreterFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\FieldQuery\FieldQueryInterpreter;
use PoP\ComponentModel\ComponentConfiguration as ComponentModelComponentConfiguration;

/**
 * Common functionality between LoadCache and SaveCache directive resolver classes
 */
trait CacheDirectiveResolverTrait
{
    /**
     * Directive enabled if caching is enabled
     *
     * @return array
     */
    public static function getClassesToAttachTo(): array
    {
        if (!ComponentModelComponentConfiguration::useComponentModelCache()) {
            return [];
        }
        return parent::getClassesToAttachTo();
    }

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
            $typeResolver->getTypeName(),
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
