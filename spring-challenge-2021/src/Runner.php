<?php

namespace CodeInGame\SpringChallenge2021;

use CodeInGame\SpringChallenge2021\State\Board;
use CodeInGame\SpringChallenge2021\State\Player;

$game = new Game(
    new Board(
        new Player(),
        new Player()
    )
);

$stateReader = new StateReader($game);

// game loop
while (true) {
    $stateReader->updateState();

    $game->applyOpponentsActions();
    echo $game->getPlayerActions();

    $game->cleanup();
}
