<?php

namespace CodeInGame\FallChallenge2020;

use CodeInGame\FallChallenge2020\Entity\Cupboard;
use CodeInGame\FallChallenge2020\Factory\Printer;
use CodeInGame\FallChallenge2020\Entity\Book;
use CodeInGame\FallChallenge2020\Entity\Recipe;
use CodeInGame\FallChallenge2020\Entity\Item;
use CodeInGame\FallChallenge2020\Entity\Spell;

class Game
{
    /**
     * @var gameState
     */
    private $gameState;

    /**
     * @var Brewer
     */
    private $brewer;

    /**
     * @var Mage
     */
    private $mage;

    public function __construct(GameState $gameState, Brewer $brewer, Mage $mage)
    {
        $this->gameState = $gameState;
        $this->hats['brewer'] = $brewer;
        $this->hats['mage'] = $mage;
    }

    public function getGameState(): GameState
    {
        return $this->gameState;
    }

    public function process(): string
    {
        // Supplies!
        $cupboard = $this->gameState->getPlayerCupboard();

        // Actions!
        $orders = $this->gameState->getOrders();
        $spells = $this->gameState->getPlayerSpells();

        // Start by seeing if there are any potions we can make
        $brewable = $cupboard->listUseable($orders);

        // If we can make something, do!
        if (count($brewable->list()) > 0) {
            return $this->hats['brewer']->makeRecipe($brewable);
        }

        // Okay, we can't make anything. Can we cast anything?
        $castable = $cupboard->listUseable($spells);

        // If we can't cast anything, rest!
        if (count($castable->list()) == 0) {
            return 'REST';
        }

        // Find the most valuable recipe to start working towards
        $recipe = $this->hats['brewer']->getBestRecipe($cupboard, $orders);

        // Find the most valuable spell for the recipe (probably FIREBALL)
        $spell = $this->hats['mage']->getBestSpell($cupboard, $castable, $recipe);

        // FIREBALL!!!!
        return $this->hats['mage']->castSpell($spell);
    }
}
