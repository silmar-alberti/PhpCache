<?php

namespace PhpCache\Adapters;

use PhpCache\Interfaces\ConnectionAdapterInterface;

class RedisAdapter implements ConnectionAdapterInterface
{
    protected $connectionData = [
        'host' => '127.0.0.1',
        'port' => '6379',
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

    public function open(): bool
    {
        $connect = $this->redis->connect($this->connectionData['host'], $this->connectionData['port']);
        if ($connect === false) {
            throw new \Exception('Can\'t open connection with host');
        }
        if (!empty($credentials['auth'])) {
            $auth = $this->redis->auth($credentials['auth']);
            if ($auth === false) {
                throw new \Exception('Connection auth fail');
            }
        }

        return true;
    }

    public function get(string $key): string
    {
        return $this->redis->get($key);
    }

    public function set(string $key, string $value, int $lifeTime): bool
    {
        return $this->redis->set($key, $value, $lifeTime);
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
