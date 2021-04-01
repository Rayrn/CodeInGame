<?php

namespace CodeInGame\FallChallenge2020;

use CodeInGame\FallChallenge2020\Entity\Cupboard;
use CodeInGame\FallChallenge2020\Factory\Printer;
use CodeInGame\FallChallenge2020\Entity\Book;
use CodeInGame\FallChallenge2020\Entity\Recipe;

class Game
{
    /**
     * @var gameState
     */
    private $gameState;

    /**
     * @var Printer
     */
    private $printer;

    public function __construct(GameState $gameState, Printer $printer)
    {
        $this->gameState = $gameState;
        $this->printer = $printer;
    }

    public function getGameState(): GameState
    {
        return $this->gameState;
    }

    public function process(): string
    {
        // Start by seeing if there are any potions we can make
        $brewable = $this->getBrewable();

        if (!$brewable) {
            return 'BREW ' . reset($brewable->list())->getId();
        }

        // If we can't brew anything, find the most valuable potion to start working towards


        // Output the ID of the potion we made
        return 'WAIT';
    }

    private function getBrewable(): Book
    {
        $brewable = array_filter($this->gameState->getOrders()->list(), function (Recipe $recipe) {
            return $this->gameState->getPlayerCupboard()->canMake($recipe);
        });

        usort($brewable, function (Recipe $recipeA, Recipe $recipeB) {
            return $recipeA->getPrice() < $recipeB->getPrice();
        });

        return $this->printer->writeBook(...$brewable);
    }

    private function getEffort()
    {
        foreach ($this->gameState->getOrders() as $recipe) {
            # code...
        }
    }
}
