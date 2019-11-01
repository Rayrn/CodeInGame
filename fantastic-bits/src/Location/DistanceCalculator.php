<?php

namespace CodeInGame\FantasticBits\Location;

class DistanceCalculator
{
    /**
     * Get the distance between two positions
     *
     * @param Position $positionA
     * @param Position $positionB
     * @return int
     */
    private function getDistance(Position $positionA, Position $positionB): int
    {
        $x = abs($positionA->getX() - $positionB->getX());
        $y = abs($positionA->gety() - $positionB->gety());

        return (int) sqrt(($x * $x) + ($y * $y));
    }

    /**
     * Get the nearest entity to a position
     *
     * @param Position $position
     * @param EntityCollection $collection
     * @return ?Entity
     */
    private function getNearestEntity(Position $position, EntityCollection $collection): ?Entity
    {
        $minDistance = null;
        $nearest = null;

        foreach ($collection->listEntities() as $entity) {
            $distance = $this->getDistance($position, $entity->getPosition());

            if ($distance < $minDistance || is_null($minDistance)) {
                $minDistance = $distance;
                $nearest = $entity;
            }
        }

        return $nearest;
    }
}
