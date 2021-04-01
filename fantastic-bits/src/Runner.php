<?php

namespace CodeInGame\FantasticBits;

use CodeInGame\FantasticBits\Location\DistanceCalculator;

$game = new Game(new StateReader(), new DistanceCalculator());
$game->init();

// game loop
while (true) {
    $game->updateState();

    foreach ($game->getActions() as $action) {
        echo $action . PHP_EOL;
    }
}
