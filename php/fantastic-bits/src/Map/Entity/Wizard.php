<?php

namespace CodeInGame\FantasticBits\Map\Entity;

use CodeInGame\FantasticBits\Location\Position;

class Wizard extends AbstractEntity
{
    private const RADIUS = 400;

    public function __construct(int $id, Position $position, Position $heading, int $state)
    {
        parent::__construct($id, self::RADIUS, $position, $heading, $state);
    }

    public function getTeam(): int
    {
        return $this->team;
    }

    public function setTeam(int $team): void
    {
        $this->team = $team;
    }
}
