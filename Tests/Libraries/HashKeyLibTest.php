<?php

namespace Tests\Libraries;

use PhpCache\Libraries\HashKeyLib;
use PHPUnit\Framework\TestCase;

class HashKeyLibTest extends TestCase
{
    const KEY_PATTERN = '/^[a-fA-F0-9]+$/';
    const KEY_WITH_PREFIX_PATTERN = '/^\w+_[a-fA-F0-9]+$/';
    public function testCreateHash()
    {
        $hash = HashKeyLib::createHashKey("test");
        $this->assertIsString($hash);
        $this->assertTrue(preg_match(self::KEY_PATTERN, $hash) === 1, 'hash not match with default pattern');
    }
    
    public function testCreateHashWithPrefix()
    {
        $namespace = 'testNamespace';
        $hash = HashKeyLib::createHashKey("test", $namespace);
        $this->assertIsString($hash);
        $this->assertEquals(0, strpos($hash, $namespace), 'hash not match with default pattern when prefix defined');
        $this->assertTrue(preg_match(self::KEY_WITH_PREFIX_PATTERN, $hash) === 1, 'hash not match with default pattern when prefix defined');
    }

    public function testCompareHash()
    {
        $hashBaseContent = 'test';
        $hashA = HashKeyLib::createHashKey($hashBaseContent);
        $hashB = HashKeyLib::createHashKey($hashBaseContent);

        $this->assertEquals($hashA, $hashB, 'divergent hash with same base content');
    }
}
