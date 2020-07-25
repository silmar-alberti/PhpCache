<?php

namespace Tests\Libraries;

use PhpCache\Adapters\RedisAdapter;
use PhpCache\Libraries\HashKeyLib;
use PhpCache\Libraries\IgBinaryLib;
use PhpCache\Libraries\PhpCache;
use PhpCache\Models\PhpCacheSettingsModel;
use PHPUnit\Framework\TestCase;

class PhpCacheTest extends TestCase {
    const REDIS_TEST_PARAMS = [
        'host' => 'host.docker.internal'
    ];

    public function testDefault()
    {
        $adapter = new RedisAdapter(self::REDIS_TEST_PARAMS);
        $serianizer = new IgBinaryLib();
        $hash = new HashKeyLib();
        $settings = new PhpCacheSettingsModel(
            $adapter,
            $serianizer, 
            $hash
        );
        $a = new PhpCache($settings);
        $b = $a->get("teste");
    }
}