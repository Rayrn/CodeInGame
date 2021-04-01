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
}
