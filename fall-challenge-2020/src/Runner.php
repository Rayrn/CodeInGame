<?php

namespace CodeInGame\FallChallenge2020;

use CodeInGame\FallChallenge2020\Factory\Printer;
use CodeInGame\FallChallenge2020\Factory\Workshop;

$game = new Game(new GameState(), new Printer());
$stateReader = new StateReader($game, new Printer(), new Workshop());

// game loop
while (true) {
    $stateReader->updateState();

    echo $game->process() . PHP_EOL;
}
