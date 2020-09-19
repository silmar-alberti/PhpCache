<?php

namespace PhpCache\Libraries;

use PhpCache\Interfaces\SerializeInterface;

class IgBinaryLib implements SerializeInterface
{
    public function serialize($data)
    {
        return igbinary_serialize($data);
    }

    public function unSerialize($data)
    {
        return igbinary_unserialize($data);
    }
}
