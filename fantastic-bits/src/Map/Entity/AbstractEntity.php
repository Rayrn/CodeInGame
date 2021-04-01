<?php

namespace CodeInGame\FantasticBits\Map\Entity;

use CodeInGame\FantasticBits\Location\Position;
use CodeInGame\FantasticBits\Map\Interfaces\Identifiable;
use CodeInGame\FantasticBits\Map\Interfaces\Mappable;
use CodeInGame\FantasticBits\Map\Interfaces\Moveable;

abstract class AbstractEntity implements Identifiable, Mappable, Moveable
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Position
     */
    protected $heading;

    /**
     * @var Position
     */
    protected $position;

    /**
     * @var int
     */
    protected $radius;

    /**
     * @var int
     */
    protected $state;

    public function __construct(int $id, int $radius, Position $position, Position $heading, int $state)
    {
        $this->id = $id;
        $this->heading = $heading;
        $this->position = $position;
        $this->radius = $radius;
        $this->state = $state;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getHeading(): Position
    {
        return $this->heading;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function getRadius(): int
    {
        return $this->radius;
    }

    public function getState(): bool
    {
        return (bool) $this->state;
    }

    public function setState(bool $state): void
    {
        $this->state = $state;
    }
}
