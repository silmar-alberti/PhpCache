<?php

namespace Tests\WithExternalDependencies;

use PhpCache\Adapters\RedisAdapter;
use PHPUnit\Framework\TestCase;

class RedisAdapterTest extends TestCase
{
    const REDIS_TEST_PARAMS = [
        'host' => 'host.docker.internal'
    ];
    const TEST_LIFE_TIME = 1;
    const PREFIX = 'testKey';
    private $redis;
    private $key;
    private $value;
    public function setUp(): void
    {
        $this->key = uniqid(self::PREFIX);
        $this->value = date("Y-m-d H:i:s");
        $this->redis = new RedisAdapter(self::REDIS_TEST_PARAMS);
    }

    public function testSetCacheEntry()
    {
        $this->redis->open();
        $wasSaved = $this->redis->set($this->key, $this->value, self::TEST_LIFE_TIME);
        $this->assertTrue($wasSaved, 'Error on save cache entrie');

        $cachedValue = $this->redis->get($this->key);
        $this->assertEquals($this->value, $cachedValue, 'Error on set CacheContent');

        $this->redis->close();
        // delay for close connection;
        sleep(1);
        $this->assertFalse($this->redis->get($this->key));
    }

    public function testNotOpenConnection()
    {
        $this->expectException(\RedisException::class);
        $this->redis->set($this->key, $this->value, self::TEST_LIFE_TIME);
    }

    public function testGetNotFoundEntry()
    {
        $this->redis->open();
        $cachedValue = $this->redis->get($this->key);
        $this->assertFalse($cachedValue);
    }

    public function testExpireTimeLife()
    {
        $this->redis->open();
        $this->redis->set($this->key, $this->value, self::TEST_LIFE_TIME);

        sleep(self::TEST_LIFE_TIME + 1);
        $cachedValue = $this->redis->get($this->key);
        $this->assertFalse($cachedValue, 'Error on expire Cache');
    }

    public function testUnlink()
    {
        $this->redis->open();
        $this->redis->set($this->key, $this->value, self::TEST_LIFE_TIME);
        $this->redis->unlink($this->key);

        $cachedValue = $this->redis->get($this->key);
        $this->assertFalse($cachedValue, 'Error on erase Cache');
    }

    public function testUnlinkAll()
    {
        $key1 = "{$this->key}_1";
        $this->redis->open();
        $this->redis->set($this->key, $this->value, self::TEST_LIFE_TIME);
        $this->redis->set($key1, $this->value, self::TEST_LIFE_TIME);

        $this->redis->unlinkAll(self::PREFIX . '*');

        $cachedValue = $this->redis->get($this->key);
        $this->assertFalse($cachedValue, 'Error erase Cache');
        $cachedValue = $this->redis->get($key1);
        $this->assertFalse($cachedValue, 'Error erase Cache');
    }
}