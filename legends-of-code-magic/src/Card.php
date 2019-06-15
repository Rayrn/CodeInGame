<?php

namespace CodeInGame\LegendsOfCodeMagic;

class Card
{
    private $instanceId;
    private $number;
    private $name;
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
        string $name,
        string $type,
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

    public function getInstanceId(): int
    {
        return $this->instanceId;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCost(): int
    {
        return $this->cost;
    }

    public function getAttack(): int
    {
        return $this->attack;
    }

    public function getDefense(): int
    {
        return $this->defense;
    }

    public function getAbilities(): string
    {
        return $this->abilities;
    }

    public function getMyHealthChange(): int
    {
        return $this->myHealthChange;
    }

    public function getOpponentHealthChange(): int
    {
        return $this->opponentHealthChange;
    }

    public function getDraw(): int
    {
        return $this->draw;
    }
}
