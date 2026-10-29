<?php

namespace CodeInGame\SpringChallenge2021\State;

class Board
{
    /**
     * The game lasts 24 days: 0-23
     *
     * @var int
     */
    private $day;

    /**
     * The base score you gain from the next COMPLETE action
     *
     * @var int
     */
    private $nutrients;

    /**
     * Bot
     *
     * @var Player
     */
    private $playerOne;

    /**
     * AI
     *
     * @var Player
     */
    private $playerTwo;

    public function __construct(Player $playerOne, Player $playerTwo)
    {
        $this->day = 0;
        $this->nutrients = 0;
        $this->playerOne = $playerOne;
        $this->playerTwo = $playerTwo;
    }

    public function getDay(): int
    {
        return $this->day;
    }

    public function getNutrients(): int
    {
        return $this->nutrients;
    }

    public function getPlayerOne(): Player
    {
        return $this->playerOne;
    }

    public function getPlayerTwo(): Player
    {
        return $this->playerTwo;
    }

    public function setDay(int $day): void
    {
        $this->day = $day;
    }

    public function setNutrients(int $nutrients): void
    {
        $this->nutrients = $nutrients;
    }

    public function setPlayerOne(Player $playerOne): void
    {
        $this->playerOne = $playerOne;
    }

    public function setPlayerTwo(Player $playerTwo): void
    {
        $this->playerTwo = $playerTwo;
    }
}
