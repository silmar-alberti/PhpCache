<?php

namespace PhpCache\Interfaces;

interface SerializeInterface
{
    /**
     * @param mixed $data data to serialize
     * @return string
     */
    public function serialize($data);
    /**
     * @param string $data
     * @return mixed unserialized data;
     */
    public function unSerialize($data);
}
