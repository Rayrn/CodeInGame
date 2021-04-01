<?php

namespace CodeInGame\FallChallenge2020;

class Debug
{
    public function __construct($entity)
    {
        error_log(var_export($entity, true));
    }
}
