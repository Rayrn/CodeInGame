<?php

namespace CodeInGame\FantasticBits\Location;

use CodeInGame\FantasticBits\Debug;
use CodeInGame\FantasticBits\Map\Entity\AbstractEntity;
use CodeInGame\FantasticBits\Map\Entity\EntityCollection;

class DistanceCalculator
{
    public function getDistance(Position $positionA, Position $positionB): int
    {
        $x = abs($positionA->getX() - $positionB->getX());
        $y = abs($positionA->gety() - $positionB->gety());

        return (int) sqrt(($x * $x) + ($y * $y));
    }

    public function getNearestEntity(Position $position, EntityCollection $collection): ?AbstractEntity
    {
        $minDistance = null;
        $nearest = null;

        foreach ($collection as $entity) {
            $distance = $this->getDistance($position, $entity->getPosition());

            if ($distance < $minDistance || is_null($minDistance)) {
                $minDistance = $distance;
                $nearest = $entity;
            }
        }

        return $nearest;
    }

    public function getPreferredEntity(EntityCollection $collectionA, EntityCollection $collectionB): array
    {
        $nearest = [];
        foreach ($collectionB as $target) {
            if ($target->getState() === true) {
                continue;
            }

            $entity = $this->getNearestEntity($target->getPosition(), $collectionA);
            $nearest[$entity->getId()][] = $target;
        }

        return $nearest;
    }
}
