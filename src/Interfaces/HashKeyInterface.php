<?php

namespace PhpCache\Interfaces;

interface HashKeyInterface
{
    /**
     * @param string $baseContent --- base content to generate hash
     * @param string $prefix --- prefix to identificate key "namespace"
     * no recomendable long prefx
     * @return string
     */
    public function getKey($baseContent, $prefix = '', $eTag = '');
}
