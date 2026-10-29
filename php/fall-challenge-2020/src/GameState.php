<?php

namespace CodeInGame\FallChallenge2020;

use CodeInGame\FallChallenge2020\Entity\Book;
use CodeInGame\FallChallenge2020\Entity\Cupboard;

class GameState
{
    /**
     * @var Cupboard
     */
    private $opponentCupboard;

    /**
     * @var Cupboard
     */
    private $opponentSpells;

    /**
     * @var Cupboard
     */
    private $playerCupboard;

    /**
     * @var Cupboard
     */
    private $playerSpells;

    /**
     * @var Book
     */
    private $orders;

    public function getOrders(): ?Book
    {
        return $this->orders;
    }

    public function getOpponentCupboard(): ?Cupboard
    {
        return $this->opponentCupboard;
    }

    public function getOpponentSpells(): ?Book
    {
        return $this->opponentSpells;
    }

    public function getPlayerCupboard(): ?Cupboard
    {
        return $this->playerCupboard;
    }

    public function getPlayerSpells(): ?Book
    {
        return $this->playerSpells;
    }

    public function setOrders(Book $orders)
    {
        $this->orders = $orders;
    }

    public function setOpponentCupboard(Cupboard $cupboard)
    {
        $this->opponentCupboard = $cupboard;
    }

    public function setOpponentSpells(Book $spells)
    {
        $this->opponentSpells = $spells;
    }

    public function setPlayerCupboard(Cupboard $cupboard)
    {
        $this->playerCupboard = $cupboard;
    }

    public function setPlayerSpells(Book $spells)
    {
        $this->playerSpells = $spells;
    }
}
