<?php

while (true) {
    // $actionCount: the number of spells and recipes in play
    fscanf(STDIN, "%d", $actionCount);
    for ($i = 0; $i < $actionCount; $i++) {
        // $actionId: the unique ID of this spell or recipe
        // $actionType: in the first league: BREW; later: CAST, OPPONENT_CAST, LEARN, BREW
        // $delta0: tier-0 ingredient change
        // $delta1: tier-1 ingredient change
        // $delta2: tier-2 ingredient change
        // $delta3: tier-3 ingredient change
        // $price: the price in rupees if this is a potion
        // $tomeIndex: in the first two leagues: always 0; later: the index in the tome if this is a tome spell, equal to the read-ahead tax; For brews, this is the value of the current urgency bonus
        // $taxCount: in the first two leagues: always 0; later: the amount of taxed tier-0 ingredients you gain from learning this spell; For brews, this is how many times you can still gain an urgency bonus
        // $castable: in the first league: always 0; later: 1 if this is a castable player spell
        // $repeatable: for the first two leagues: always 0; later: 1 if this is a repeatable player spell
        fscanf(STDIN, "%d %s %d %d %d %d %d %d %d %d %d", $actionId, $actionType, $delta0, $delta1, $delta2, $delta3, $price, $tomeIndex, $taxCount, $castable, $repeatable);
    }
    for ($i = 0; $i < 2; $i++) {
        // $inv0: tier-0 ingredients in inventory
        // $score: amount of rupees
        fscanf(STDIN, "%d %d %d %d %d", $inv0, $inv1, $inv2, $inv3, $score);
    }
    // in the first league: BREW <id> | WAIT; later: BREW <id> | CAST <id> [<times>] | LEARN <id> | REST | WAIT
    echo("BREW 0\n");
}
