<?php

namespace PhpCache\Libraries;

use PhpCache\Interfaces\SerializeInterface;

class DefaultSerializerLib implements SerializeInterface
{
    public function serialize($data): string
    {
        return serialize($data);
    }

    public function unSerialize(string $data)
    {
        return unserialize($data);
    }
}
