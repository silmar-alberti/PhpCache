<?php

namespace PhpCache\Libraries;

use PhpCache\Interfaces\HashKeyInterface;

class HashKeyLib implements HashKeyInterface
{
    /**
     * @param string $baseContent --- base content to generate hash
     * @param string $prefix --- prefix to identificate key "namespace"
     * no recomendable long prefx
     * 
     * @return string hashKey
     */
    public function getKey($baseContent, $prefix = '', $eTag = '')
    {
        $key = sha1($baseContent . $eTag);
        if ($prefix) {
            $key = "{$prefix}_{$key}";
        }

        return $key;
    }
}
