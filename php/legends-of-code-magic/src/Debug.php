<?php

namespace CodeInGame\LegendsOfCodeMagic;

class Debug
{
    /**
     * Output data to the console
     *
     * @param mixed $entity
     */
    public function __construct($entity)
    {
        error_log(var_export($entity, true));
    }
}
