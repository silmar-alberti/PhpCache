<?php

namespace PhpCache\Adapters;

use PhpCache\Interfaces\ConnectionAdapterInterface;
use PhpCache\Models\CacheObjectModel;

class RedisAdapter implements ConnectionAdapterInterface
{
    protected $connectionData = [
        'host' => '127.0.0.1',
        'port' => 6379,
        'timeout' => 0.2,
        'reserved' => null,
        'retryInterval' => 0.0,
        'readTimeout' => 0.0,
        'auth' => []
    ];

    protected $redis;
    protected $throwErrors = true;

    public function __construct(array $connectionParams, $throwErrors = true)
    {
        $this->throwErrors = $throwErrors;
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
        return $this->callFunctionAndCatchErrors(function () {
            $this->redis->connect(
                $this->connectionData['host'],
                $this->connectionData['port'],
                $this->connectionData['timeout'],
                $this->connectionData['retryInterval'],
                $this->connectionData['readTimeout']
            );

            if (!empty($this->connectionData['auth'])) {
                $this->redis->auth($this->connectionData['auth']);
            }
            return true;
        }, [], true);
    }

    public function get(string $key)
    {
        return $this->callFunctionAndCatchErrors(
            function ($key) {
                $cache = $this->redis->get($key);
                if ($cache !== false) {
                    return $cache;
                }
            },
            [$key]
        );
    }

    public function incr(CacheObjectModel $cacheObject): int
    {
        return $this->callFunctionAndCatchErrors(function ($cacheObject) {
            if (!$this->redis->exists($cacheObject->key)) {
                $this->set($cacheObject);
                return 1;
            }
            return $this->redis->incrBy($cacheObject->key, $cacheObject->value);
        }, [
            $cacheObject
        ]);
    }

    public function set(CacheObjectModel $cacheObject): bool
    {
        return $this->callFunctionAndCatchErrors(
            function ($cacheObject) {
                return $this->redis->set(
                    $cacheObject->key,
                    $cacheObject->value,
                    $cacheObject->lifeTime
                );
            },
            [$cacheObject],
            true
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

    private function callFunctionAndCatchErrors(callable $function, $params = [], $defaultReturn = null)
    {
        try {
            return call_user_func_array($function, $params);
        } catch (\RedisException $e) {
            if ($this->throwErrors === true) {
                throw $e;
            } else {
                error_log('REDIS ADAPTER ERROR ' . $e->getMessage());
            }
        }
        return $defaultReturn;
    }
}
