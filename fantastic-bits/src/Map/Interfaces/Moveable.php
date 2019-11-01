<?php

namespace CodeInGame\FantasticBits\Map\Interfaces;

use CodeInGame\FantasticBits\Map\Position;

interface Moveable
{
    public function getHeading(): Position;
}
