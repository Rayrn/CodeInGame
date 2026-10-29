<?php

namespace CodeInGame\FallChallenge2020;

class Debug
{
    public function __construct(...$entity)
    {
        foreach ($entity as $item) {
            error_log(var_export($item, true));
        }
    }
}
