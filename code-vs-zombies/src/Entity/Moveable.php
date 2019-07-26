<?php

namespace CodeInGame\CodeVsZombies\Entity;

interface Moveable
{
    /**
     * Set the next Position for the Entity
     *
     * @param Position $postition
     * @return void
     */
    public function setNextPosition(Position $position): void;

    /**
     * Get the next Entity Position
     *
     * @return Position
     */
    public function getNextPosition(): Position;
}
