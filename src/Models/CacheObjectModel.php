<?php

namespace PhpCache\Models;
class CacheObjectModel
{
    protected $key;
    protected $lifeTime = 3600;
    protected $value;

    /**
     * @param int $lifeTime  life time in seconds 
     * default is 3600
     */
    public function __construct(string $key, $value, int $lifeTime)
    {
        $this->key = $key;
        $this->lifeTime = $lifeTime;
        $this->value = $value;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getLifeTime(): int
    {
        return $this->lifeTime;
    }

    public function getValue()
    {
        return $this->value;
    }
    
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }
    
    public function setLifeTime($lifeTime)
    {
        $this->lifeTime = $lifeTime;
        return $this;
    }
    
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}
