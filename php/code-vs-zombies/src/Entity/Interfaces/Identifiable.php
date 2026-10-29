<?php

namespace CodeInGame\CodeVsZombies\Entity\Interfaces;

interface Identifiable
{
    /**
     * Get the Entity ID
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Get the Entity type
     *
     * @return string
     */
    public function getType(): string;
}
