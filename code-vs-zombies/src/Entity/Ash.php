<?php

namespace CodeInGame\CodeVsZombies\Entity;

class Ash implements Mappable
{
    /**
     * @var Position
     */
    protected $position;

    /**
     * Set the Position for the Entity
     *
     * @param Position $postition
     * @return void
     */
    public function setPosition(Position $position): void
    {
        $this->position = $position;
    }

    /**
     * Get the Entity Position
     *
     * @return Position
     */
    public function getPosition(): Position
    {
        return $this->position;
    }
}
