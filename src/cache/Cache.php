<?php
namespace PoP\Engine\Cache;
use Psr\Cache\CacheItemPoolInterface;
use PoP\Hooks\Contracts\HooksAPIInterface;

class Cache implements CacheInterface
{
    use ReplaceInstanceDataWithPlaceholdersTrait;
    private $cacheItemPool;
    private $hooksAPI;

    public function __construct(CacheItemPoolInterface $cacheItemPool, HooksAPIInterface $hooksAPI)
    {
        $this->cacheItemPool = $cacheItemPool;
        $this->hooksAPI = $hooksAPI;

        // When a plugin is activated/deactivated, ANY plugin, delete the corresponding cached files
        // This is particularly important for the MEMORY, since we can't set by constants to not use it
        $this->hooksAPI->addAction(
            'popcms:componentInstalledOrUninstalled',
            function () {
                $this->cacheItemPool->clear();
            }
        );

        // Save all deferred cacheItems
        $this->hooksAPI->addAction(
            'popcms:shutdown',
            function () {
                $this->cacheItemPool->commit();
            }
        );
    }

    protected function getKey($id, $type)
    {
        return $type . '.' . $id;
    }

    protected function getCacheItem($id, $type)
    {
        return $this->cacheItemPool->getItem($this->getKey($id, $type));
    }

    public function getCache($id, $type)
    {
        $cacheItem = $this->getCacheItem($id, $type);
        if ($cacheItem->isHit()) {

            // Return the file contents
            $content = $cacheItem->get();

            // Inject the current request data in place of the placeholders (pun not intended!)
            return $this->replacePlaceholdersWithCurrentExecutionData($content);
        }

        return false;
    }

    public function storeCache($id, $type, $content)
    {
        // Before saving the cache, replace the data specific to this execution with generic placeholders
        $content = $this->replaceCurrentExecutionDataWithPlaceholders($content);
        
        $cacheItem = $this->getCacheItem($id, $type);
        $cacheItem->set($content);
        $this->cacheItemPool->saveDeferred($cacheItem);
    }

    public function getCacheByModelInstance($type)
    {
        $model_instance_id = ModelInstanceProcessor_Utils::getModelInstanceId();
        return $this->getCache($model_instance_id, $type);
    }

    public function storeCacheByModelInstance($type, $content)
    {
        $model_instance_id = ModelInstanceProcessor_Utils::getModelInstanceId();
        return $this->storeCache($model_instance_id, $type, $content);
    }
}
