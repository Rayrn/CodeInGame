<?php

namespace CodeInGame\LegendsOfCodeMagic;

class CardCollection
{
    protected $collection = [];

    public function add(Card $card, int $location): void
    {
        $this->collection[$card->getInstanceId()] = ['card' => $card, 'location' => $location];
    }

    public function clear(): void
    {
        $this->collection = [];
    }

    public function find(int $cardNumber, int $location): ?Card
    {
        foreach ($this->collection as $cardData) {
            if ($cardData['location'] !== $location) {
                continue;
            }

            if ($cardData['card']->getNumber() == $cardNumber) {
                return $cardData['card'];
            }
        }

        return null;
    }

    public function list(): array
    {
        return $this->collection;
    }

    public function remove(int $instanceId): void
    {
        unset($this->collection[$instanceId]);
    }
}
