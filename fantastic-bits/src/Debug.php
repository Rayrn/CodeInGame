<?php

namespace CodeInGame\FantasticBits;

class Debug
{
    public function __construct(...$entity)
    {
        foreach ($entity as $output) {
            error_log(print_r($output, true));
        }
    }
}
