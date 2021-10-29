<?php

namespace CodeInGame\FallChallenge2020\Entity;

use ArrayIterator;
use IteratorAggregate;

class Book implements IteratorAggregate
{
    /**
     * @var Items[]
     */
    private $items = [];

    public function add(Item ...$items)
    {
        foreach ($items as $item) {
            $this->items[$item->getId()] = $item;
        }
    }

    public function remove(Item $item): void
    {
        unset($this->items[$item->getId()]);
    }

    public function list(): array
    {
        return $this->items;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->list());
    }
}
