<?php

namespace PhpCache\Interfaces;

interface SerializeInterface
{
    /**
     * @param mixed $data data to serialize
     */
    public function serialize($data): string;
    /**
     * @return mixed unserialized data;
     */
    public function unSerialize(string $data);
}
