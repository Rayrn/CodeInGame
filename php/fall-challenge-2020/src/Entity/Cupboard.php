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

    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function getRupees(): int
    {
        return $this->rupees;
    }

    public function canMake(Item $item): bool
    {
        foreach ($item->getIngredientCost() as $key => $count) {
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

        foreach ($item->getIngredientCost() as $key => $count) {
            $this->ingredients[$key] - $count;
        }

        return true;
    }

    public function listUseable(Book $book): Book
    {
        $newBook = clone $book;

        foreach ($newBook as $item) {
            if (!$this->canMake($item)) {
                $newBook->remove($item);
            }
        }

        return $newBook;
    }

    public function listMissingIngredients(Item $item): array
    {
        $required = $item->getIngredientCost();

        $missing = [];
        foreach ($required as $key => $count) {
            if ($this->ingredients[$key] < $count) {
                $missing[$key] = abs($this->ingredients[$key] - $count);
            }
        }

        return $missing;
    }
}
