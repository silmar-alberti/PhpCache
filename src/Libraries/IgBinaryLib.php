<?php

namespace PhpCache\Libraries;

use PhpCache\Interfaces\SerializeInterface;

class IgBinaryLib implements SerializeInterface
{
    public function serialize($data): string
    {
        return igbinary_serialize($data);
    }
    
    public function unSerialize(string $data)
    {
        return igbinary_unserialize($data);
    }
}
