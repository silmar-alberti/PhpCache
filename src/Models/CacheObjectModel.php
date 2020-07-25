<?php

class CacheObjectModel
{
    protected $key;
    protected $lifeTime = 3600;
    protected $value;

    public function __construct(string $key, $value, int $lifeTime)
    {
        $this->key = $key;
        $this->lifeTime = $lifeTime;
        $this->value = $value;
    }

    public function getKey()
    {
        return $this;
    }

    public function getLifeTime()
    {
        return $this;
    }

    public function getValue()
    {
        return $this;
    }
    
    public function setKey($key)
    {
        $this->key = $key;
    }

    public function setLifeTime($lifeTime)
    {
        $this->lifeTime = $lifeTime;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }
}
