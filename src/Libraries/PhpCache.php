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
     * Caution!! $baseKeyContent is serialized to get cacheKey,  and $cacheValue is serialized to storage
     * if your entries not serializabe, cause error on store and recovery cache entries
     * @param mixed $cacheValue
     * @param int $lifeTime
     * @param string $prefix
     * @param string $eTag
     * @return bool
     */
    public function set($baseKeyContent, $cacheValue, $lifeTime = 3600, $prefix = '', $eTag = '')
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
     * @param string $prefix
     * @param string $etag
     * @return mixed|null
     */
    public function get($baseKeyContent, $prefix = '', $eTag = '')
    {
        $key = $this->getCacheKey($baseKeyContent, $prefix, $eTag);
        $data = $this->settings->adapter->get($key);
        if ($data !== null) {
            return $this->settings->serializer->unserialize($data);
        }
        return null;
    }

    /**
     * @param string|array|Serializable $baseKeyContent 
     * @param int $increaseValue
     * @param int $lifeTime
     * @param string $prefix
     * @param string $etag
     * @return int
     */
    public function increase($baseKeyContent,  $increaseValue = 1,  $lifeTime = 3600, $prefix = '', $eTag = '')
    {
        $key = $this->getCacheKey($baseKeyContent, $prefix, $eTag);
        return $this->settings->adapter->incr(new CacheObjectModel(
            $key,
            $increaseValue,
            $lifeTime
        ));
    }

    /**
     * if function and args math in cache entry return cachedData
     * else call function and store result in cache
     
     * @param callable $function
     * @param array $args
     * @param string $functionIdentfier
     * @param int $lifeTime
     * @param string $eTag
     * @return bool
     */
    public function cacheFunction($function, $args, $functionIdentifier, $lifeTime = 3600, $eTag = '')
    {
        $key = $this->getCacheKey($args, $functionIdentifier, $eTag);
        $data = $this->settings->adapter->get($key);
        if ($data !== null) {
            return $this->settings->serializer->unserialize($data);
        }

        return $this->callAndStoreResult($function, $args, $key, $lifeTime);
    }

    private function callAndStoreResult($function, $args, $key, $lifeTime)
    {
        $returnedData = call_user_func_array($function, $args);
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
