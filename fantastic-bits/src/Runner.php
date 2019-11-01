<?php

namespace CodeInGame\FantasticBits;

$game = new Game(new StateReader());
$game->init();

new Debug($game);

// game loop
while (true) {
    $game->updateState();

    new Debug($game);

    foreach ($game->getActions() as $action) {
        echo $action . PHP_EOL;
    }
}
