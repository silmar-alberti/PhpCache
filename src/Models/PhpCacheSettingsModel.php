<?php

namespace PhpCache\Models;

use PhpCache\Interfaces\ConnectionAdapterInterface;
use PhpCache\Interfaces\HashKeyInterface;
use PhpCache\Interfaces\SerializeInterface;

class PhpCacheSettingsModel
{
    public $adapter;
    public $serializer;
    public $hash;

    public function __construct(
        ConnectionAdapterInterface $adapter,
        SerializeInterface $serializer,
        HashKeyInterface $hash
    ) {
        $this->adapter = $adapter;
        $this->serializer = $serializer;
        $this->hash = $hash;
    }
}
