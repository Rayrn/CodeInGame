<?php

namespace CodeInGame\FantasticBits\Map\Component;

use CodeInGame\FantasticBits\Location\Position;

class Goal
{
    private const GOAL_WIDTH = 4000;

    /**
     * @var Position
     */
    private $centre;

    /**
     * @var GoalPost
     */
    private $northPost;

    /**
     * @var GoalPost
     */
    private $southPost;

    public function __construct(Position $position)
    {
        $this->centre = $position;
        $this->northPost = $this->getNorthPost($position);
        $this->southPost = $this->getSouthPost($position);
    }

    public function getGoalTop(): int
    {
        return $this->northPost->getY() - ($this->northPost->getRadius() / 2);
    }

    public function getGoalCentre(): Position
    {
        return $this->centre;
    }

    public function getGoalBottom(): int
    {
        return $this->southPost->getY() + ($this->northPost->getRadius() / 2);
    }

    private function getNorthPost(Position $position): GoalPost
    {
        $yShift = self::GOAL_WIDTH / 2;

        return new GoalPost(
            new Position($position->getX(), $position->getY() + $yShift)
        );
    }

    private function getSouthPost(Position $position): GoalPost
    {
        $yShift = self::GOAL_WIDTH / 2;

        return new GoalPost(
            new Position($position->getX(), $position->getY() - $yShift)
        );
    }
}
