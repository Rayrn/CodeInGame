<?php

namespace CodeInGame\LegendsOfCodeMagic;

$game = new Game(new Player(), new Opponent());
$stateReader = new StateReader($game);

// game loop
while (true) {
    $stateReader->updateState();

    $game->applyOpponentsActions();
    echo $game->getPlayerActions();

    $game->cleanup();
}

/**
 * To debug (equivalent to var_dump)
 */
function debug($var)
{
    error_log(var_export($var, true));
}
