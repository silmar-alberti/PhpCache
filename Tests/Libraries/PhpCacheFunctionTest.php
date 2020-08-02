<?php

namespace Tests\Libraries;

use PhpCache\Libraries\HashKeyLib;
use PhpCache\Libraries\IgBinaryLib;
use PhpCache\Libraries\PhpCache;
use PhpCache\Models\PhpCacheSettingsModel;
use PHPUnit\Framework\TestCase;
use Tests\TestLibs\RedisAdapterTestLib;

class PhpCacheFunctionTest extends TestCase
{

    const TEST_BASE_CONTENT = 'asdf';
    const TEST_VALUE = 'valueasdf';
    const TEST_IDENTIFIER = 'testIdent';
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

    public function testGetFromCache()
    {
        $phpCache = new PhpCache($this->testSettings);
        $result = $phpCache->cacheFunction(
            function () {
            },
            [],
            self::TEST_IDENTIFIER
        );
        $this->assertEquals(self::TEST_VALUE, $result);
    }

    public function testGetCallFunction()
    {
        $testValue = self::TEST_VALUE . 'ds';

        $redisLib = new RedisAdapterTestLib();
        $this->testSettings->adapter = $redisLib->getMockAdapter(false);

        $phpCache = new PhpCache($this->testSettings);
        $result = $phpCache->cacheFunction(
            function () use ($testValue) {
                return $testValue;
            },
            [],
            self::TEST_IDENTIFIER
        );
        $this->assertEquals($testValue, $result);
    }

    public function testGetCallFunctionWithArgs()
    {
        $testValue = self::TEST_VALUE . 'ds';

        $redisLib = new RedisAdapterTestLib();
        $this->testSettings->adapter = $redisLib->getMockAdapter(false);

        $phpCache = new PhpCache($this->testSettings);
        $result = $phpCache->cacheFunction(
            function ($testValue) {
                return $testValue;
            },
            [$testValue],
            self::TEST_IDENTIFIER
        );
        $this->assertEquals($testValue, $result);
    }
}
