<?php

namespace PhpCache\Libraries;

use PhpCache\Interfaces\SerializeInterface;
use PhpCache\Models\PhpCacheException;

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
