<?php

namespace CodeInGame\FantasticBits\Location;

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

    public function getNearestFreeEntity(Position $position, EntityCollection $collection): ?AbstractEntity
    {
        $freeEntities = new EntityCollection();

        foreach ($collection as $entity) {
            if ($entity->getState() === false) {
                $freeEntities->add($entity);
            }
        }

        return $this->getNearestEntity($position, $freeEntities);
    }
}
