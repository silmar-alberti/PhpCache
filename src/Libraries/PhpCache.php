<?php

namespace PhpCache\Libraries;

use PhpCache\Models\CacheObjectModel;
use PhpCache\Models\PhpCacheException;
use PhpCache\Models\PhpCacheSettingsModel;
use Serializable;

class PhpCache
{
    protected $settings;

    public function __construct(PhpCacheSettingsModel $settings)
    {
        $this->settings = $settings;
        $this->settings->adapter->open();
    }

    /**
     * @param mixed $baseKeyContent
     * @param mixed $cacheValue
     * Caution!! $baseKeyContent is serialized to get cacheKey,  and $cacheValue is serialized to storage
     * if your entries not serializabe, cause error on store and recovery cache entries
     */
    public function set($baseKeyContent, $cacheValue, $lifeTime = 3600, string $prefix = '', string $eTag = ''): bool
    {
        $key = $this->getCacheKey($baseKeyContent, $prefix, $eTag);

        $cacheObject = new CacheObjectModel(
            $key,
            $this->settings->serializer->serialize($cacheValue),
            $lifeTime
        );
        return $this->settings->adapter->set($cacheObject);
    }

    /**
     * @param string|array|Serializable $baseKeyContent 
     */
    public function get($baseKeyContent, string $prefix = '', string $eTag = '')
    {
        $key = $this->getCacheKey($baseKeyContent, $prefix, $eTag);
        $data = $this->settings->adapter->get($key);
        if ($data !== null) {
            return $this->settings->serializer->unserialize($data);
        }
        return null;
    }

    /**
     * if function and args math in cache entry return cachedData
     * else call function and store result in cache
     */
    public function cacheFunction(callable $function, array $args, string $functionIdentifier, $lifeTime = 3600, string $eTag = '')
    {
        $key = $this->getCacheKey($args, $functionIdentifier, $eTag);
        $data = $this->settings->adapter->get($key);
        if ($data !== null) {
            return $this->settings->serializer->unserialize($data);
        }

        return $this->callAndStoreResult($function, $args, $key, $lifeTime);
    }

    private function callAndStoreResult(callable $function, array $args, string $key, int $lifeTime)
    {
        $returnedData = $function(...$args);
        $cacheObject = new CacheObjectModel(
            $key,
            $this->settings->serializer->serialize($returnedData),
            $lifeTime
        );
        if ($this->settings->adapter->set($cacheObject)) {
            return $returnedData;
        }

        throw new PhpCacheException('Error on save cache data');
    }

    private function getCacheKey($baseKeyContent, $prefix, $eTag)
    {
        $stringKeyBase = $this->settings->serializer->serialize($baseKeyContent);
        return $this->settings->hash->getKey($stringKeyBase, $prefix, $eTag);
    }
}
