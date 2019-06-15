<?php

namespace CodeInGame\LegendsOfCodeMagic;

class CardFactory
{
    private $dictionary = [];

    public function addTemplate(
        int $number,
        int $type,
        int $cost,
        int $attack,
        int $defense,
        string $abilities,
        int $myHealthChange,
        int $opponentHealthChange,
        int $draw
    ) {
        if (empty($this->dictionary[$number])) {
            $this->dictionary[$number] = [$number, $type, $cost, $attack, $defense, $abilities, $myHealthChange, $opponentHealthChange, $draw];
        }
    }

    public function create(int $number, int $instanceId): Card
    {
        $template = $this->dictionary[$number];

        return new Card($instanceId, ...$template);
    }
}
