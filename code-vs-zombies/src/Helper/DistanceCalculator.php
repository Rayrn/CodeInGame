<?php

namespace CodeInGame\CodeVsZombies\Helper;

use CodeInGame\CodeVsZombies\Entity\Ash;
use CodeInGame\CodeVsZombies\Entity\EntityCollection;
use CodeInGame\CodeVsZombies\Entity\Position;

class DistanceCalculator
{
    private const ASH_MOVEMENT = 1000;
    private const ASH_RANGE = 2000;
    private const ZOMBIE_MOVEMENT = 400;

    /**
     * Calculate distance in turns between Ash and a set of Entities
     *
     * @param Ash $ash
     * @param EntityCollection $collection
     * @return int[]
     */
    public function ashToCollection(Ash $ash, EntityCollection $collection): array
    {
        $entites = [];
        foreach ($collection->list() as $entity) {
            $distance = $this->getDistance($ash->getPosition(), $entity->getPosition()) / self::ASH_MOVEMENT;

            $entites[$entity->getId()] = intval($distance);
        }

        return $entites;
    }

    /**
     * Calculate distance in turns between two sets of Entities (nearest only)
     *
     * @param EntityCollection $collectionA
     * @param EntityCollection $collectionB
     * @return int[]
     */
    public function collectionToCollection(EntityCollection $collectionA, EntityCollection $collectionB): array
    {
        $entites = [];
        foreach ($collectionA->list() as $entity) {
            $nearest = $this->getNearestEntity($entity->getPosition(), $collectionB);

            if (is_null($nearest)) {
                // If null is returned then the second entity collection was empty. Skip.
                break;
            }

            $entites[$entity->getId()] = intval($this->getDistance($entity, $nearest->getPosition()));
        }

        return $entites;
    }

    /**
     * Get the distance between two positions
     *
     * @param Position $positionA
     * @param Position $positionB
     * @return int
     */
    public function getDistance(Position $positionA, Position $positionB): int
    {
        $x = abs($postiionA->getX() - $postiionB->getX());
        $y = abs($postiionA->gety() - $postiionB->gety());

        return (int) sqrt(($x * $x) + ($y * $y));
    }

    /**
     * Get the nearest entity to a position
     *
     * @param Position $position
     * @param EntityCollection $collection
     * @return ?Entity
     */
    public function getNearestEntity(Position $position, EntityCollection $collection): ?Entity
    {
        $minDistance = null;
        $nearest = null;

        foreach ($collection->list() as $entity) {
            $distance = $this->getDistance($position, $$entity->getPosition());

            if ($distance < $minDistance || is_null($minDistance)) {
                $minDistance = $distance;
                $nearest = $entity;
            }
        }

        return $nearest;
    }
}
