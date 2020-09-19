<?php

namespace PhpCache\Models;

class CacheObjectModel
{
    public $key;
    public $lifeTime = 3600;
    public $value;

    /**
     * @param string $key
     * @param int $lifeTime  life time in seconds 
     * default is 3600
     */
    public function __construct($key, $value, $lifeTime)
    {
        $this->key = $key;
        $this->lifeTime = $lifeTime;
        $this->value = $value;
    }
}
