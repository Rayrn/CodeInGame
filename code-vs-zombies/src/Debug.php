<?php

namespace CodeInGame\CodeVsZombies;

class Debug
{
    public function __construct($entity)
    {
        error_log(print_r($entity, true));
    }
}
