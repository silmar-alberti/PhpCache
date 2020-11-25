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
     * @return bool
     */
    public function close();

    /**
     * open Connection
     * @return bool
     */
    public function open();

    /**
     * get cache entrie
     * @param string $key
     * @return string|null content or null when not found
     */
    public function get($key);
    /**
     * increment entry on cache 
     * @param CacheObjectModel $cacheObject
     * @return int new value of entry
     */
    public function incr($cacheObject);

    /**
     * store cache item 
     * @return bool
     */
    public function set(CacheObjectModel $cacheObject);

    /**
     * delete cache entry
     * @param string $key
     * @return bool
     */
    public function unlink($key);

    /**
     * delete cache entry math with filter
     * @param string $keyFilter
     * @return bool
     */
    public function unlinkAll($keyFilter);
}
