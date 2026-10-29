<?php

namespace CodeInGame\CodeVsZombies;

use CodeInGame\CodeVsZombies\Entity\Ash;
use CodeInGame\CodeVsZombies\Entity\Entity;
use CodeInGame\CodeVsZombies\Entity\EntityCollection;
use CodeInGame\CodeVsZombies\Location\DistanceCalculator;

$game = new Game(
    new StateReader(),
    new Ash(),
    new EntityCollection(Entity::HUMAN),
    new EntityCollection(Entity::ZOMBIE),
    new DistanceCalculator()
);

// game loop
while (true) {
    $game->updateState();

    echo $game->getAction() . PHP_EOL;
}
