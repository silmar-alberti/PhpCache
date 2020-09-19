<?php

namespace PhpCache\Libraries;

use PhpCache\Interfaces\SerializeInterface;

class DefaultSerializerLib implements SerializeInterface
{
    public function serialize($data)
    {
        return serialize($data);
    }

    public function unSerialize($data)
    {
        return unserialize($data);
    }
}
