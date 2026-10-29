<?php

namespace CodeInGame\FallChallenge2020;

use CodeInGame\FallChallenge2020\Factory\Printer;
use CodeInGame\FallChallenge2020\Factory\Workshop;
use CodeInGame\FallChallenge2020\Helper\PrepTimeCalculator;
use CodeInGame\FallChallenge2020\Worker\Brewer;
use CodeInGame\FallChallenge2020\Worker\Mage;

// I miss autowiring already
$game = new Game(
    new GameState(),
    new Brewer(
        new PrepTimeCalculator()
    ),
    new Mage()
);
$stateReader = new StateReader(
    $game,
    new Printer(),
    new Workshop()
);

// game loop
while (true) {
    $stateReader->updateState();

    echo $game->process() . PHP_EOL;
}
