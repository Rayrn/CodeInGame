<?php

namespace CodeInGame\FantasticBits;

use CodeInGame\FantasticBits\Location\DistanceCalculator;
use CodeInGame\FantasticBits\Location\Position;
use CodeInGame\FantasticBits\Map\Team;
use CodeInGame\FantasticBits\Map\Component\Goal;

class Game
{
    private const SCORE_LEFT = 0;
    private const SCORE_RIGHT = 1;

    /**
     * @var DistanceCalculator
     */
    private $distanceCalculator;

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

    public function __construct(StateReader $stateReader, DistanceCalculator $distanceCalculator)
    {
        $this->distanceCalculator = $distanceCalculator;
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
        [$this->myTeam, $this->oppTeam, $this->snaffles] = $this->stateReader->getGameState();
    }

    public function getActions(): array
    {
        $actions = [];

        foreach ($this->myTeam->getWizards() as $wizard) {
            $command = $wizard->getState() ? 'THROW' : 'MOVE';

            switch ($command) {
                case 'THROW':
                    $target = $this->opponentGoal->getGoalCentre();
                    $speed = 400;
                    break;
                case 'MOVE':
                default:
                    $snaffle = $this->distanceCalculator->getNearestFreeEntity($wizard->getPosition(), $this->snaffles);
                    $snaffle->setState(true);

                    $target = $snaffle->getPosition();
                    $speed = 100;
                    break;
            }

            $actions[] = "$command {$target->getX()} {$target->getY()} $speed";
        }

        return $actions;
    }
}
