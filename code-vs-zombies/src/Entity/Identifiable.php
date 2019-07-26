<?php

namespace CodeInGame\CodeVsZombies\Entity;

interface Identifiable
{
    /**
     * Get the Entity ID
     *
     * @return int
     */
    public function getId(): int;
}
