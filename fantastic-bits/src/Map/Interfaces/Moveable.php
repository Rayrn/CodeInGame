<?php

namespace CodeInGame\FantasticBits\Map\Interfaces;

use CodeInGame\FantasticBits\Location\Position;

interface Moveable
{
    public function getHeading(): Position;
}
