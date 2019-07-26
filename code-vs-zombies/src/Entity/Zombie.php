<?php

namespace CodeInGame\CodeVsZombies\Entity;

class Zombie extends Entity implements Moveable
{
    /**
     * @var int
     */
    private $nextPosition;

    /**
     * Create a new Zombie Entity
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct(self::ZOMBIE, $id);
    }

    /**
     * Set the next Position for the Entity
     *
     * @param Position $postition
     * @return void
     */
    public function setNextPosition(Position $position): void
    {
        $this->nextPosition = $position;
    }

    /**
     * Get the next Entity Position
     *
     * @return Position
     */
    public function getNextPosition(): Position
    {
        return $this->nextPosition;
    }
}
