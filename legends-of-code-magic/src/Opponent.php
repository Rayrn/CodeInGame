<?php

namespace CodeInGame\LegendsOfCodeMagic;

class Opponent extends Player
{
    private $cardsInHand = 0;
    private $actions = [];

    public function updateState(): void
    {
        parent::updateState();

        fscanf(STDIN, "%d %d", $cardsInHand, $action);

        $this->cardsInHand = $cardsInHand;

        for ($i = 0; $i < $action; $i++) {
            [$cardNumber, $action] = explode(' ', stream_get_line(STDIN, 20 + 1, "\n"));

            $this->actions[] = ['cardNumber' => $cardNumber, 'action' => $action];
        }
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
