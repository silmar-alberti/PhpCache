<?php

namespace PhpCache\Interfaces;

interface HashKeyInterface {
    /**
     * @param string $baseContent --- base content to generate hash
     * @param string $prefix --- prefix to identificate key "namespace"
     * no recomendable long prefx
     */
    public function getKey(string $baseContent, string $prefix = ''): string ;
}