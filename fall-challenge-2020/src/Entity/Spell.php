<?php

namespace CodeInGame\FallChallenge2020\Entity;

use InvalidArgumentException;

class Spell extends Item
{
    /**
     * @var bool
     */
    private $castable;

    public function __construct(int $id, array $ingredients, bool $castable)
    {
        $this->id = $id;

        $this->ingredients = array_map(function ($cost) {
            return (int) $cost;
        }, $ingredients);

        $this->castable = $castable;
    }

    public function isCastable(): bool
    {
        return $this->castable;
    }
}
