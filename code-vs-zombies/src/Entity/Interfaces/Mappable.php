<?php

namespace CodeInGame\CodeVsZombies\Entity\Interfaces;

use CodeInGame\CodeVsZombies\Location\Position;

interface Mappable
{
    /**
     * Set the Position for the Entity
     *
     * @param Position $postition
     * @return void
     */
    public function setPosition(Position $position): void;

    /**
     * Get the Entity Position
     *
     * @return Position
     */
    public function getPosition(): Position;
}
