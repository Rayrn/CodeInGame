<?php

namespace CodeInGame\FantasticBits;

use CodeInGame\FantasticBits\Location\DistanceCalculator;
use CodeInGame\FantasticBits\Location\Position;
use CodeInGame\FantasticBits\Map\Team;
use CodeInGame\FantasticBits\Map\Component\Goal;
use CodeInGame\FantasticBits\Map\Entity\EntityCollection;

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
        $defaultActions = $this->getDefaultActions($this->myTeam->getWizards());
        $throwActions = $this->getThrowActions($this->myTeam->getWizards()->listActive());
        $moveActions = $this->getMoveActions($this->myTeam->getWizards()->listInactive());

        $actions = array_replace($defaultActions, $throwActions, $moveActions);

        ksort($actions);

        return $actions;
    }

    private function getDefaultActions(EntityCollection $wizards): array
    {
        $actions = [];

        foreach ($wizards as $wizard) {
            $actions[$wizard->getId()] = "MOVE 8000 3750 100";
        }

        return $actions;
    }

    private function getThrowActions(EntityCollection $haveSnaffle): array
    {
        $actions = [];

        foreach ($haveSnaffle as $wizard) {
            $target = $this->opponentGoal->getGoalCentre();
            $actions[$wizard->getId()] = "THROW {$target->getX()} {$target->getY()} 400";
        }

        return $actions;
    }

    private function getMoveActions(EntityCollection $needSnaffle): array
    {
        $actions = [];

        foreach ($needSnaffle as $wizard) {
            $targetList = $this->distanceCalculator->getPreferredEntity($needSnaffle, $this->snaffles);

            $filteredSnaffles = isset($targetList[$wizard->getId()])
                ? new EntityCollection(...$targetList[$wizard->getId()])
                : $this->snaffles;

            $snaffle = $this->distanceCalculator->getNearestEntity($wizard->getPosition(), $filteredSnaffles);

            if (!$snaffle) {
                continue;
            }

            $snaffle->setState(true);

            $actions[$wizard->getId()] = "MOVE {$snaffle->getPosition()->getX()} {$snaffle->getPosition()->getY()} 100";

            $needSnaffle->remove($wizard->getId());
        }

        return $actions;
    }
}
