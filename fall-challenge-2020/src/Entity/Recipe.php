<?php

namespace CodeInGame\FallChallenge2020\Entity;

use InvalidArgumentException;

class Recipe extends Item
{
    /**
     * @var int
     */
    private $price;

    public function __construct(
        int $id,
        array $ingredients,
        int $price
    ) {
        $this->id = $id;

        $this->ingredients = array_map(function ($cost) {
            return (int) $cost;
        }, $ingredients);

        $this->price = $price;
    }

    public function getPrice(): int
    {
        return $this->price;
    }
}
