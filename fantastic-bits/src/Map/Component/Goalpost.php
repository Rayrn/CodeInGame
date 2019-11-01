<?php

namespace CodeInGame\FantasticBits\Map\Component;

use CodeInGame\FantasticBits\Map\Position;
use CodeInGame\FantasticBits\Map\Interfaces\Mappable;

class Goalpost implements Mappable
{
    private const RADIUS = 300;

    /**
     * @var Position
     */
    private $position;

    /**
     * @var Int
     */
    private $radius;

    public function __construct(Position $position)
    {
        $this->position = $position;
        $this->radius = self::RADIUS;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function getRadius(): int
    {
        return $this->radius;
    }
}
