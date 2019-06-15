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

    public function updateState(): void
    {
        fscanf(STDIN, "%d %d %d %d %d", $health, $mana, $cardsInDeck, $rune, $draw);

        $this->health = $health;
        $this->mana = $mana;
        $this->rune = $rune;
        $this->draw = $draw;
    }

    public function getDeckDefinition()
    {
        return $this->deck;
    }

    public function isDeckComplete(): bool
    {
        return count($this->deck->list()) !== 30;
    }
}
