<?php

namespace CodeInGame\CodeVsZombies;

use CodeInGame\CodeVsZombies\Entity\Ash;
use CodeInGame\CodeVsZombies\Entity\Entity;
use CodeInGame\CodeVsZombies\Entity\EntityCollection;

$game = new Game(
    new StateReader(),
    new Ash(),
    new EntityCollection(Entity::HUMAN),
    new EntityCollection(Entity::ZOMBIE)
);

// game loop
while (true) {
    $game->updateState();

    echo $game->getActions();

    $game->cleanup();
}
