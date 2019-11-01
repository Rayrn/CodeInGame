<?php

namespace CodeInGame\FantasticBits;

use CodeInGame\FantasticBits\Map\Position;
use CodeInGame\FantasticBits\Map\Team;
use CodeInGame\FantasticBits\Map\Component\Goal;

class Game
{
    private const SCORE_LEFT = 0;
    private const SCORE_RIGHT = 1;

    /**
     * @var Goal
     */
    private $myGoal;

    /**
     * @var Team
     */
    private $myTeam;

    /**
     * @var Goal
     */
    private $opponentGoal;

    /**
     * @var Team
     */
    private $oppTeam;

    /**
     * @var Snaffle[]
     */
    private $snaffles;

    /**
     * @var StateReader
     */
    private $stateReader;

    public function __construct(StateReader $stateReader)
    {
        $this->stateReader = $stateReader;
    }

    public function init(): void
    {
        $playDirection = $this->stateReader->getPlayDirection();
        $leftGoal = new Goal(new Position(0, 3750));
        $rightGoal = new Goal(new Position(16000, 3750));

        if ($playDirection == self::SCORE_LEFT) {
            $this->myGoal = $leftGoal;
            $this->opponentGoal = $rightGoal;
        }

        if ($playDirection == self::SCORE_RIGHT) {
            $this->myGoal = $rightGoal;
            $this->opponentGoal = $leftGoal;
        }
    }

    public function updateState(): void
    {
        [$this->myTeam, $this->oppTeam, $this->snaffles] = $this->stateReader->updateState();
    }
}
