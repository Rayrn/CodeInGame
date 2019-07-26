<?php

namespace CodeInGame\CodeVsZombies\Entity;

class Position
{
    /**
     * @var int
     */
    private $x;

    /**
     * @var int
     */
    private $y;

    /**
     * Create a new Position
     *
     * @param int $id;
     */
    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Get the X position
     *
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * Get the Y position
     *
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }
}
