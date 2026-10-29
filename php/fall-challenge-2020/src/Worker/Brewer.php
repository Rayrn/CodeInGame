<?php

namespace CodeInGame\FallChallenge2020\Worker;

use CodeInGame\FallChallenge2020\Entity\Cupboard;
use CodeInGame\FallChallenge2020\Entity\Book;
use CodeInGame\FallChallenge2020\Entity\Recipe;
use CodeInGame\FallChallenge2020\Helper\PrepTimeCalculator;

class Brewer
{
    /**
     * @var PrepTimeCalculator
     */
    private $prepTimeCalculator;

    public function __construct(PrepTimeCalculator $prepTimeCalculator)
    {
        $this->prepTimeCalculator = $prepTimeCalculator;
    }

    /**
     * Generate the make recipe command for the most expensive recipe in the book
     */
    public function makeRecipe(Book $book)
    {
        usort($book, function (Recipe $recipeA, Recipe $recipeB) {
            return $recipeA->getPrice() < $recipeB->getPrice();
        });

        return 'BREW ' . reset($book->list())->getId();
    }

    /**
     * Find the most valuable recipe (based on time to make) in the current round
     */
    public function getBestRecipe(Cupboard $cupboard, Book $orders): Recipe
    {
        // Check how long each will take
        $prepTimes = [];
        foreach ($orders as $recipe) {
            $prepTime = $this->prepTimeCalculator->calculatePrepTime($cupboard, $recipe);

            $prepTimes[$prepTime][] = $recipe;
        }

        // Sort into ROI => Time
        $roi = [];
        foreach ($prepTimes as $time => $recipes) {
            foreach ($recipes as $recipe) {
                $actionRoI = ($recipe->getPrice() / $time) * 1000;

                $roi[$actionRoI][$time][] = $recipe;
            }
        }

        // Get the most valuable ROI set first
        $mostValuable = $roi[max(array_keys($roi))];

        // Then find the quickest
        $quickest = $mostValuable[min(array_keys($mostValuable))];

        // Return the first item (as they're all theoretically identical at this point)
        return reset($quickest);
    }
}
