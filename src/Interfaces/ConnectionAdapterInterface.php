<?php

namespace PhpCache\Interfaces;

use PhpCache\Models\CacheObjectModel;

interface ConnectionAdapterInterface
{
    /**
     * create connection adapter
     */
    public function __construct(array $connectionParams);

    /**
     * close Connection
     */
    public function close(): bool;

    /**
     * open Connection
     */
    public function open(): bool;

    /**
     * get cache entrie
     * @return string|null content or null when not found
     */
    public function get(string $key);

    /**
     * increment entry on cache 
     * @return int new value of entry
     */
    public function incr(CacheObjectModel $cacheObject): int;

    /**
     * store cache item 
     */
    public function set(CacheObjectModel $cacheObject): bool;

    /**
     * delete cache entry
     */
    public function unlink(string $key): bool;

    /**
     * delete cache entry math with filter
     */
    public function unlinkAll(string $keyFilter): bool;
}
