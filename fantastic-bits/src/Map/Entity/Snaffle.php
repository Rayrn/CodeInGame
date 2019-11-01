<?php

namespace CodeInGame\FantasticBits\Map\Entity;

use CodeInGame\FantasticBits\Map\Position;

class Snaffle extends AbstractEntity
{
    private const RADIUS = 150;

    public function __construct(int $id, Position $position, Position $heading, int $state)
    {
        parent::__construct($id, self::RADIUS, $position, $heading, $state);
    }
}
