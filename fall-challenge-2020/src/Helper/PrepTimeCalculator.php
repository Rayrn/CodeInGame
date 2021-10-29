<?php

namespace CodeInGame\FallChallenge2020\Helper;

use CodeInGame\FallChallenge2020\Entity\Cupboard;
use CodeInGame\FallChallenge2020\Entity\Recipe;

class PrepTimeCalculator
{
    /**
     * How long would it take us to make this recipe?
     */
    public function calculatePrepTime(Cupboard $cupboard, Recipe $recipe): int
    {
        $missing = $cupboard->listMissingIngredients($recipe);

        // For now, lets just hard-code this as the spells all exchange one ingredient for the level above
        $timeToPrep = 0;
        foreach ($missing as $level => $count) {
            $timeToPrep += $level * $count;
        }

        // Add in level 0 generation
        $missingRawIngredientCount = array_sum($cupboard->getIngredients()) - array_sum($recipe->getIngredientCost());

        if ($missingRawIngredientCount > 0) {
            $timeToPrep += ceil($missingRawIngredientCount / 2);
        }

        // Add in rests
        $timeToPrep += max($missing);

        return $timeToPrep;
    }
}
