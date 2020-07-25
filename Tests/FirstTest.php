<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class FirstTest extends TestCase {
    public function testEcho()
    {
        $redis = new \Redis();
        $test = $redis->connect('host.docker.internal');
    }
}