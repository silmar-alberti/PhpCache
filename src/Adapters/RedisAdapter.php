<?php

namespace PhpCache\Adapters;

use PhpCache\Interfaces\ConnectionAdapterInterface;
use PhpCache\Models\CacheObjectModel;

class RedisAdapter implements ConnectionAdapterInterface
{
    protected $connectionData = [
        'host' => '127.0.0.1',
        'port' => 6379,
        'auth' => []
    ];

    protected $redis;

    public function __construct(array $connectionParams)
    {
        $this->connectionData = array_merge($this->connectionData, $connectionParams);
        $this->redis = new \Redis();
    }

    public function close(): bool
    {
        if ($this->redis->isConnected()) {
            return $this->redis->close();
        }
        return true;
    }

    /**
     * @throws \RedisException
     */
    public function open(): bool
    {
        $this->redis->connect($this->connectionData['host'], $this->connectionData['port']);

        if (!empty($this->connectionData['auth'])) {
            $this->redis->auth($this->connectionData['auth']);
        }

        return true;
    }

    public function get(string $key)
    {
        $cache = $this->redis->get($key);
        if ($cache !== null) {
            return $cache;
        }
        return null;
    }

    public function set(CacheObjectModel $cacheObject): bool
    {
        return $this->redis->set(
            $cacheObject->key,
            $cacheObject->value,
            $cacheObject->lifeTime
        );
    }

    public function unlink(string $key): bool
    {
        if (is_callable(array($this->redis, 'unlink'))) {
            return (bool) $this->redis->unlink($key);
        } else {
            return (bool) $this->redis->del($key);
        }
    }

    public function unlinkAll(string $keyFilter): bool
    {
        $keys = $this->redis->keys($keyFilter);
        $numberOfKeys = count($keys);
        $deletedKeys =  0;
        if (is_callable(array($this->redis, 'unlink'))) {
            $deletedKeys = $this->redis->unlink(...$keys);
        } else {
            $deletedKeys = $this->redis->del(...$keys);
        }

        return $deletedKeys === $numberOfKeys;
    }
}
