<?php

namespace Tests\TestLibs;

use PhpCache\Adapters\RedisAdapter;
use PHPUnit\Framework\TestCase;

class RedisAdapterTestLib extends TestCase {
    const REDIS_TEST_PARAMS = [
        'host' => 'host.docker.internal'
    ];

    public function getAdapter() : RedisAdapter {
        return new RedisAdapter(self::REDIS_TEST_PARAMS);
    }

    public function getMockAdapter($getReturn = '', $setReturn = true){
        $mockAdapter = $this->createMock(RedisAdapter::class, []);
        $mockAdapter->method('set')->willReturn($setReturn);
        $mockAdapter->method('get')->willReturn($getReturn);
        return $mockAdapter;
    }
    
}