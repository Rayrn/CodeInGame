<?php

namespace CodeInGame\LegendsOfCodeMagic\Card;

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

    public function get(string $instanceId): ?Card
    {
        return $this->collection[$instanceId] ?? null;
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

    public function listAll(): array
    {
        return $this->collection;
    }

    public function listForLocation(int $location): array
    {
        $list = [];

        foreach ($this->collection as $data) {
            if ($data['location'] === $location) {
                $list[] = $data['card'];
            }
        }

        return $list;
    }

    public function remove(int $instanceId): void
    {
        unset($this->collection[$instanceId]);
    }
}
