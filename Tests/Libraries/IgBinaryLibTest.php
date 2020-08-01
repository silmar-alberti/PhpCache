<?php

namespace Tests\Libraries;

use PhpCache\Libraries\IgBinaryLib;
use PHPUnit\Framework\TestCase;
use Tests\TestLibs\SerializeTestLib;

class IgBinaryLibTest extends TestCase
{
    private $serializer;

    public function setUp(): void
    {
        $this->serializer =  new IgBinaryLib();
    }

    public function testSerializeString()
    {
        $string = 'testSerializer';
        $serialized = $this->serializer->serialize($string);

        $this->assertNotEquals($string, $serialized);

        $unserialized = $this->serializer->unserialize($serialized);
        $this->assertEquals($string, $unserialized);
    }

    public function testSerializeArray()
    {
        $array = [
            'a' => 'testSerializer',
            'testSerializer'
        ];

        $serialized = $this->serializer->serialize($array);

        $this->assertNotEquals($array, $serialized);

        $unserialized = $this->serializer->unserialize($serialized);
        $this->assertEquals($array, $unserialized);
    }

    public function testSerializeObject()
    {
        $object = new SerializeTestLib();

        $serialized = $this->serializer->serialize($object);

        $this->assertNotEquals($object, $serialized);

        $unserialized = $this->serializer->unserialize($serialized);
        $this->assertEquals($object, $unserialized);
    }

    public function testSerializeMixed()
    {
        $array = [
            "a" => 0,
            "b" => 'test',
            "c" => new SerializeTestLib()
        ];

        $serialized = $this->serializer->serialize($array);

        $unserialized = $this->serializer->unserialize($serialized);
        $this->assertEquals($array['a'], $unserialized['a']);
        $this->assertEquals($array['b'], $unserialized['b']);
        $this->assertEquals($array['c'], $unserialized['c']);
    }
}
