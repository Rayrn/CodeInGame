<?php

namespace CodeInGame\CodeVsZombies;

class Debug
{
    public function __construct($entity)
    {
        error_log(var_export($entity, true));
    }
}
