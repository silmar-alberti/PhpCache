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
     * @param string|array|Serializable $baseKeyContent 
     */
    public function set($baseKeyContent, $cacheValue, $lifeTime = 3600, string $prefix = ''): bool
    {
        $key = $this->getCacheKey($baseKeyContent, $prefix);

        $cacheObject = new CacheObjectModel($key, $cacheValue, $lifeTime);
        return $this->save($cacheObject);
    }
    
    /**
     * @param string|array|Serializable $baseKeyContent 
     */
    public function get($baseKeyContent, string $prefix = '')
    {
        $key = $this->getCacheKey($baseKeyContent, $prefix);
        $data = $this->settings->adapter->get($key);
        if($data !== false) {
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
        $stringKeyBase = $this->serializeData($baseKeyContent); 
        return $this->settings->hash->getKey($stringKeyBase, $prefix);
    }
    
    /**
     * @throws PhpCacheException
     */
    private function serializeData($data): string
    {
        if(is_string($data) || is_numeric($data)){
            return $data;
        }else if($this->isSerializable($data)){
            return $this->settings->serializer->serialize($data);
        }else {
            throw new PhpCacheException('Not serializable');
        }
    }

    private function isSerializable($baseKeyContent){
        return is_array($baseKeyContent) || $baseKeyContent instanceof Serializable;
    }
}
