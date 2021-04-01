<?php

namespace CodeInGame\LegendsOfCodeMagic;

$game = new Game(new Player\Player(), new Player\Opponent());
$stateReader = new StateReader($game);

// game loop
while (true) {
    $stateReader->updateState();

    $game->applyOpponentsActions();
    echo $game->getPlayerActions();

    $game->cleanup();
}
