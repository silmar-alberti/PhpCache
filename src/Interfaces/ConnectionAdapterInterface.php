<?php

namespace PhpCache\Interfaces;

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
     * @return string|bool content or false when not found
     */
    public function get(string $key): string;
    
    /**
    * store cache item 
    * @param int $lifeTime ttl in seconds
    */
    public function set(string $key, string $value, int $lifeTime): bool;
    
    /**
     * delete cache entry
     */
    public function unlink(string $key): bool;

    /**
     * delete cache entry math with filter
     */
    public function unlinkAll(string $keyFilter): bool;
}
