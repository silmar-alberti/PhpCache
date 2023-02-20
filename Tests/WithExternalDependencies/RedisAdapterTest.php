<?php

namespace Tests\WithExternalDependencies;

use PhpCache\Adapters\RedisAdapter;
use PhpCache\Models\CacheObjectModel;
use PHPUnit\Framework\TestCase;

class RedisAdapterTest extends TestCase
{
    const REDIS_TEST_PARAMS = [
        'host' => 'host.docker.internal'
    ];
    const TEST_LIFE_TIME = 1;
    const PREFIX = 'testKey';
    /**
     * @var RedisAdapter $redis
     */
    private $redis;
    private $key;
    private $value;
    public function setUp(): void
    {
        $this->key = uniqid(self::PREFIX);
        $this->value = date("Y-m-d H:i:s");
        $this->redis = new RedisAdapter(self::REDIS_TEST_PARAMS, true);
    }

    public function testCantConnect()
    {
        $this->redis = new RedisAdapter([
            'host' => 'wrongHost',
        ]);

        $this->expectException(\RedisException::class);
        $this->redis->open();
    }


    public function testCantAuth()
    {
        $con = self::REDIS_TEST_PARAMS;
        $con['auth'] = 'test';

        $this->redis = new RedisAdapter($con, false);
        $this->assertTrue($this->redis->open());
    }

    public function testSetCacheEntry()
    {
        $this->redis->open();
        $wasSaved = $this->set($this->key, $this->value, self::TEST_LIFE_TIME);
        $this->assertTrue($wasSaved, 'Error on save cache entrie');

        $cachedValue = $this->redis->get($this->key);
        $this->assertEquals($this->value, $cachedValue, 'Error on set CacheContent');

        $this->redis->close();
        // delay for close connection;
        sleep(1);
        $this->assertNull($this->redis->get($this->key));
    }

    public function testNotOpenConnection()
    {
        $this->redis = new RedisAdapter(self::REDIS_TEST_PARAMS, false);
        $this->assertTrue($this->set($this->key, $this->value, self::TEST_LIFE_TIME));
    }

    public function testNotOpenConnectionException()
    {
        $this->expectException(\RedisException::class);
        $this->set($this->key, $this->value, self::TEST_LIFE_TIME);
    }

    public function testGetNotFoundEntry()
    {
        $this->redis->open();
        $cachedValue = $this->redis->get($this->key);
        $this->assertNull($cachedValue);
    }

    public function testExpireTimeLife()
    {
        $this->redis->open();
        $this->set($this->key, $this->value, self::TEST_LIFE_TIME);

        sleep(self::TEST_LIFE_TIME + 1);
        $cachedValue = $this->redis->get($this->key);
        $this->assertNull($cachedValue, 'Error on expire Cache');
    }

    public function testIncrement()
    {
        $this->redis->open();
        $firstValue = $this->incr('teste', 1, self::TEST_LIFE_TIME);
        $this->redis->close();
        $this->redis->open();
        $seccondValue = $this->incr('teste', 1, self::TEST_LIFE_TIME);
        $this->assertGreaterThan($firstValue, $seccondValue);
    }

    public function testExpiresIncrement()
    {
        $this->redis->open();
        $this->redis->unlink('teste');
        $firstValue = $this->incr('teste', 1, self::TEST_LIFE_TIME);
        $this->assertEquals(1, $firstValue);

        sleep(self::TEST_LIFE_TIME + 1);
        $seccondValue = $this->incr('teste', 1, self::TEST_LIFE_TIME);
        $this->assertEquals($firstValue, $seccondValue);
    }

    public function testUnlink()
    {
        $this->redis->open();
        $this->set($this->key, $this->value, self::TEST_LIFE_TIME);
        $this->redis->unlink($this->key);

        $cachedValue = $this->redis->get($this->key);
        $this->assertNull($cachedValue, 'Error on erase Cache');
    }

    public function testUnlinkAll()
    {
        $key1 = "{$this->key}_1";
        $this->redis->open();
        $this->set($this->key, $this->value, self::TEST_LIFE_TIME);
        $this->set($key1, $this->value, self::TEST_LIFE_TIME);

        $this->redis->unlinkAll(self::PREFIX . '*');

        $cachedValue = $this->redis->get($this->key);
        $this->assertNull($cachedValue, 'Error erase Cache');
        $cachedValue = $this->redis->get($key1);
        $this->assertNull($cachedValue, 'Error erase Cache');
    }

    private function set($key, $value, $lifeTime)
    {
        $cacheObject = new CacheObjectModel(
            $key,
            $value,
            $lifeTime
        );
        return $this->redis->set($cacheObject);
    }

    private function incr($key, $value, $lifeTime)
    {
        $cacheObject = new CacheObjectModel(
            $key,
            $value,
            $lifeTime
        );
        return $this->redis->incr($cacheObject);
    }
}
