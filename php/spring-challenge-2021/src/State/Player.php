<?php

namespace CodeInGame\SpringChallenge2021\State;

class Player
{
    /**
     * Current sun points
     *
     * @var int
     */
    public $sun;

    /**
     * Current score
     *
     * @var int
     */
    public $score;

    /**
     * True if asleep until the next day (AKA Passed)
     *
     * @var bool
     */
    public $isSleeping;

    public function getSun(): int
    {
        return $this->sun;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function getIsSleeping(): bool
    {
        return $this->isSleeping;
    }

    public function setSun(int $sun): void
    {
        $this->sun = $sun;
    }

    public function setScore(int $score): void
    {
        $this->score = $score;
    }

    public function setIsSleeping(bool $isSleeping): void
    {
        $this->isSleeping = $isSleeping;
    }
}
