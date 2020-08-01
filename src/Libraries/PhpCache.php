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
    public function set($baseKeyContent, $cacheValue, $lifeTime = 3600, string $prefix = ''): bool
    {
        $key = $this->getCacheKey($baseKeyContent, $prefix);

        $cacheObject = new CacheObjectModel(
            $key,
            $this->settings->serializer->serialize($cacheValue),
            $lifeTime
        );
        return $this->save($cacheObject);
    }

    /**
     * @param string|array|Serializable $baseKeyContent 
     */
    public function get($baseKeyContent, string $prefix = '')
    {
        $key = $this->getCacheKey($baseKeyContent, $prefix);
        $data = $this->settings->adapter->get($key);
        if ($data !== false) {
            return $this->settings->serializer->unserialize($data);
        }
        return false;
    }


    private function save(CacheObjectModel $cacheObject): bool
    {
        return $this->settings->adapter->set(
            $cacheObject->getKey(),
            $cacheObject->getValue(),
            $cacheObject->getLifeTime()
        );
    }

    private function getCacheKey($baseKeyContent, $prefix)
    {
        $stringKeyBase = $this->settings->serializer->serialize($baseKeyContent);
        return $this->settings->hash->getKey($stringKeyBase, $prefix);
    }
}
