<?php

namespace CodeInGame\LegendsOfCodeMagic;

$game = new Game(new CardFactory(), new Player(), new Opponent());

// game loop
while (true) {
    $game->updateState();
    $game->applyOpponentsActions();
    $playerActions = $game->getPlayerActions();

    echo implode(';', $playerActions) . "\n";

    $game->cleanup();
}

/**
 * To debug (equivalent to var_dump)
 */
function debug($var)
{
    error_log(var_export($var, true));
}
