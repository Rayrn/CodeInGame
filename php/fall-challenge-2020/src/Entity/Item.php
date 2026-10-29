<?php

namespace CodeInGame\FallChallenge2020\Entity;

use InvalidArgumentException;

class Item
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var array
     */
    protected $ingredients;

    public function getId(): int
    {
        return $this->id;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function getIngredientGain(): array
    {
        $ingredientGain = [];

        foreach ($this->ingredients as $key => $count) {
            if ($count > 0) {
                $ingredientGain[$key] = $count;
            }
        }

        return $ingredientGain;
    }

    public function getIngredientCost(): array
    {
        $ingredientGain = [];

        foreach ($this->ingredients as $key => $count) {
            if ($count < 0) {
                $ingredientGain[$key] = abs($count);
            }
        }

        return $ingredientGain;
    }
}
