<?php

namespace CodeInGame\FantasticBits\Map\Interfaces;

use CodeInGame\FantasticBits\Map\Position;

interface Mappable
{
    public function getPosition(): Position;

    public function getRadius(): int;
}
