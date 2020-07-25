<?php

namespace PhpCache\Libraries;

class HashKeyLib {
    /**
     * @param string $baseContent --- base content to generate hash
     * @param string $prefix --- prefix to identificate key "namespace"
     * no recomendable long prefx
     * 
     * @return string hashKey
     */
    public static function createHashKey($baseContent, $prefix = '')
    {
        $key = sha1($baseContent);
        if ($prefix) {
            $key = "{$prefix}_{$key}";
        }

        return $key;
    }
}