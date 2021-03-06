<?php

namespace CodeInGame\CodeVsZombies\Location;

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
     * Outputs a representation of the object as a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->x . ' ' . $this->y;
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
