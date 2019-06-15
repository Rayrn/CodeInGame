<?php

namespace CodeInGame\LegendsOfCodeMagic;

class Card
{
    private $instanceId;
    private $number;
    private $type;
    private $cost;
    private $attack;
    private $defense;
    private $abilities;
    private $myHealthChange;
    private $opponentHealthChange;
    private $draw;

    public function __construct(
        int $instanceId,
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
        $this->instanceId = $instanceId;
        $this->number = $number;
        $this->type = $type;
        $this->cost = $cost;
        $this->attack = $attack;
        $this->defense = $defense;
        $this->abilities = $abilities;
        $this->myHealthChange = $myHealthChange;
        $this->opponentHealthChange = $opponentHealthChange;
        $this->draw = $draw;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getInstanceId(): int
    {
        return $this->instanceId;
    }

    public function getAttack(): int
    {
        return $this->attack;
    }

    public function getDefense(): int
    {
        return $this->defense;
    }

    public function getCost(): int
    {
        return $this->cost;
    }
}
