<?php

namespace CodeInGame\LegendsOfCodeMagic;

class Player
{
    protected $health;
    protected $mana;
    protected $rune;
    protected $draw;

    protected $deck;

    public function __construct()
    {
        $this->deck = new CardReferenceCollection();
    }

    public function updateState(int $health, int $mana, int $rune, int $draw): void
    {
        $this->health = $health;
        $this->mana = $mana;
        $this->rune = $rune;
        $this->draw = $draw;
    }

    public function getHealth(): int
    {
        return $this->health;
    }

    public function getMana(): int
    {
        return $this->mana;
    }

    public function getRune(): int
    {
        return $this->rune;
    }

    public function getDraw(): int
    {
        return $this->draw;
    }

    public function getDeckDefinition()
    {
        return $this->deck;
    }
}
