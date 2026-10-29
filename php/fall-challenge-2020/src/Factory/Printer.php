<?php

namespace CodeInGame\FallChallenge2020\Factory;

use CodeInGame\FallChallenge2020\Entity\Book;
use CodeInGame\FallChallenge2020\Entity\Item;
use CodeInGame\FallChallenge2020\Entity\Recipe;
use CodeInGame\FallChallenge2020\Entity\Spell;

class Printer
{
    public function writeBook(Item ...$items): Book
    {
        $book = new Book();
        $book->add(...$items);

        return $book;
    }

    public function writeRecipe(int $id, array $ingredients, int $price): Recipe
    {
        return new Recipe($id, $ingredients, $price);
    }

    public function writeSpell(int $id, array $ingredients, bool $castabble): Spell
    {
        return new Spell($id, $ingredients, $castabble);
    }
}
