<?php

namespace CodeInGame\LegendsOfCodeMagic;

class Opponent extends Player
{
    private $cardsInHand = 0;
    private $actions = [];

    public function updateState(
        int $health,
        int $mana,
        int $rune,
        int $draw,
        int $cardsInHand = 0, 
        array $actions = []
    ): void {
        parent::updateState($health, $mana, $rune, $draw);

        $this->cardsInHand = $cardsInHand;
        $this->actions = $actions;
    }

    public function getCardsInHand(): int
    {
        return $this->cardsInHand;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function clearActions(): void
    {
        $this->actions = [];
    }
}
