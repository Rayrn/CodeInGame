<?php

namespace CodeInGame\FallChallenge2020\Entity;

class Cupboard
{
    /**
     * @var array
     */
    private $ingredients;

    /**
     * @var int
     */
    private $rupees;

    public function __construct(
        int $ingredientZeroCount,
        int $ingredientOneCount,
        int $ingredientTwoCount,
        int $ingredientThreeCount,
        int $rupees
    ) {
        $this->ingredients[] = $ingredientZeroCount;
        $this->ingredients[] = $ingredientOneCount;
        $this->ingredients[] = $ingredientTwoCount;
        $this->ingredients[] = $ingredientThreeCount;

        $this->rupees = $rupees;
    }

    public function canMake(Item $item): bool
    {
        foreach ($item->getIngredients() as $key => $count) {
            if ($this->ingredients[$key] - $count < 0) {
                return false;
            }
        }

        return true;
    }

    public function make(Item $item): bool
    {
        if (!$this->canMake($item)) {
            return false;
        }

        foreach ($item->getIngredients() as $key => $count) {
            $this->ingredients[$key] -= $count;
        }

        return true;
    }

    public function toArray(): array
    {
        return [
            'ingredients' => $this->ingredients,
            'rupees' => $this->rupees
        ];
    }
}
