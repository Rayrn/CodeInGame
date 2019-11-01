<?php

namespace CodeInGame\FantasticBits\Map\Components;

use CodeInGame\FantasticBits\Map\Position;

class Goal
{
    private const GOAL_WIDTH = 4000;

    public function __construct(Position $position)
    {
        $this->northPost = $this->getNorthPost($position);
        $this->southPost = $this->getSouthPost($position);
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
