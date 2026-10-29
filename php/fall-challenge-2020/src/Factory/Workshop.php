<?php

namespace CodeInGame\FallChallenge2020\Factory;

use CodeInGame\FallChallenge2020\Entity\Cupboard;

class Workshop
{
    public function build(
        int $ingredientZeroCount,
        int $ingredientOneCount,
        int $ingredientTwoCount,
        int $ingredientThreeCount,
        int $rupees
    ): Cupboard {
        return new Cupboard(
            $ingredientZeroCount,
            $ingredientOneCount,
            $ingredientTwoCount,
            $ingredientThreeCount,
            $rupees
        );
    }
}
