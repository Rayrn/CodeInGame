<?php

namespace CodeInGame\FallChallenge2020;

use CodeInGame\FallChallenge2020\Factory\Printer;
use CodeInGame\FallChallenge2020\Factory\Workshop;

class StateReader
{
    /**
     * @var Game
     */
    private $game;

    /**
     * @var Printer
     */
    private $printer;

    /**
     * @var Workshop
     */
    private $workshop;

    public function __construct(Game $game, Printer $printer, Workshop $workshop)
    {
        $this->game = $game;
        $this->printer = $printer;
        $this->workshop = $workshop;
    }

    public function updateState(): void
    {
        $this->updateRecipeCollection();
        $this->updateCupboards();
    }

    private function updateRecipeCollection(): void
    {
        $items = [
            'orders' => [],
            'playerSpells' => [],
            'opponentSpells' => []
        ];

        fscanf(STDIN, "%d", $actionCount);

        for ($i = 0; $i < $actionCount; $i++) {
            fscanf(
                STDIN,
                "%d %s %d %d %d %d %d %d %d %d %d",
                $id,
                $type,
                $ingredientCost0,
                $ingredientCost1,
                $ingredientCost2,
                $ingredientCost3,
                $price,
                $tomeIndex,
                $tax,
                $castable,
                $repeatable
            );

            switch ($type) {
                case 'BREW':
                    $items['orders'][] = $this->printer->writeRecipe(
                        $id,
                        [$ingredientCost0, $ingredientCost1, $ingredientCost2, $ingredientCost3],
                        $price
                    );
                    break;
                case 'CAST':
                    $items['playerSpells'][] = $this->printer->writeSpell(
                        $id,
                        [$ingredientCost0, $ingredientCost1, $ingredientCost2, $ingredientCost3],
                        $castable
                    );
                    break;
                case 'OPPONENT_CAST':
                    $items['opponentSpells'][] = $this->printer->writeSpell(
                        $id,
                        [$ingredientCost0, $ingredientCost1, $ingredientCost2, $ingredientCost3],
                        $castable
                    );
                    break;
                default:
                    new Debug($type);
                    break;
            }
        }

        $this->game->getGameState()->setOrders(
            $this->printer->writeBook(...$items['orders'])
        );

        $this->game->getGameState()->setPlayerSpells(
            $this->printer->writeBook(...$items['playerSpells'])
        );

        $this->game->getGameState()->setOpponentSpells(
            $this->printer->writeBook(...$items['opponentSpells'])
        );
    }

    private function updateCupboards(): void
    {
        $cupboards = [];
        for ($i = 0; $i < 2; $i++) {
            fscanf(STDIN, "%d %d %d %d %d", $ingredientZero, $ingredientOne, $ingredientTwo, $ingredientThree, $score);

            $cupboards[] = $this->workshop->build(
                $ingredientZero,
                $ingredientOne,
                $ingredientTwo,
                $ingredientThree,
                $score
            );
        }

        $this->game->getGameState()->setPlayerCupboard($cupboards[0]);
        $this->game->getGameState()->setOpponentCupboard($cupboards[1]);
    }
}
