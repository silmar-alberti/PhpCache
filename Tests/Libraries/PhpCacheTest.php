<?php

namespace Tests\Libraries;

use PhpCache\Adapters\RedisAdapter;
use PhpCache\Libraries\HashKeyLib;
use PhpCache\Libraries\IgBinaryLib;
use PhpCache\Libraries\PhpCache;
use PhpCache\Models\PhpCacheSettingsModel;
use PHPUnit\Framework\TestCase;
use Tests\TestLibs\RedisAdapterTestLib;

class PhpCacheTest extends TestCase
{

    const TEST_BASE_CONTENT = 'asdf';
    const TEST_VALUE = 'valueasdf';
    private $testSettings;

    public function setUp(): void
    {
        $serializer = new IgBinaryLib();

        $redisLib = new RedisAdapterTestLib();
        $mockAdapter = $redisLib->getMockAdapter($serializer->serialize(self::TEST_VALUE));

        $hash = new HashKeyLib();

        $this->testSettings = new PhpCacheSettingsModel(
            $mockAdapter,
            $serializer,
            $hash
        );
    }

    public function testGet()
    {
        $phpCache = new PhpCache($this->testSettings);
        $this->assertEquals(self::TEST_VALUE, $phpCache->get(self::TEST_BASE_CONTENT));
    }

    public function testSet()
    {
        $phpCache = new PhpCache($this->testSettings);
        $this->assertTrue($phpCache->set(self::TEST_BASE_CONTENT, self::TEST_VALUE));
    }

    public function testWrongSet()
    {
        $redisLib = new RedisAdapterTestLib();
        $this->testSettings->adapter = $redisLib->getMockAdapter('', false);

        $phpCache = new PhpCache($this->testSettings);
        $this->assertFalse($phpCache->set(self::TEST_BASE_CONTENT, self::TEST_VALUE));
    }

    public function testNotFound()
    {
        $redisLib = new RedisAdapterTestLib();
        $this->testSettings->adapter = $redisLib->getMockAdapter(false);

        $phpCache = new PhpCache($this->testSettings);
        $this->assertFalse($phpCache->get(self::TEST_BASE_CONTENT));
    }
}
