<?php

namespace CodeInGame\CodeVsZombies\Location;

use CodeInGame\CodeVsZombies\Debug;
use CodeInGame\CodeVsZombies\Entity\Entity;
use CodeInGame\CodeVsZombies\Entity\EntityCollection;
use CodeInGame\CodeVsZombies\Entity\Interfaces\Mappable;

class DistanceCalculator
{
    /**
     * Calculate distance between a mappable object and a set of Entities
     *
     * @param Mappable $mappable
     * @param EntityCollection $collection
     * @return int[]
     */
    public function mappableToCollection(Mappable $mappable, EntityCollection $collection): array
    {
        $entites = [];
        foreach ($collection->listEntities() as $entity) {
            $entites[$entity->getId()] = intval($this->getDistance(
                $mappable->getPosition(),
                $entity->getPosition()
            ));
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
        foreach ($collectionA->listEntities() as $entity) {
            $nearest = $this->getNearestEntity($entity->getPosition(), $collectionB);

            if (is_null($nearest)) {
                // If null is returned then the second entity collection was empty. Skip.
                break;
            }

            $entites[$entity->getId()] = intval($this->getDistance($entity->getPosition(), $nearest->getPosition()));
        }

        return $entites;
    }

    /**
     * Find the central point for a collection of entities
     *
     * @param EntityCollection $collection
     * @return Position
     */
    public function findCentralPoint(EntityCollection $collection): Position
    {
        $xSum = 0;
        $ySum = 0;

        foreach ($collection->list() as $entity) {
            $xSum += $entity->getPosition()->getX();
            $ySum += $entity->getPosition()->gety();
        }

        return new Position(
            $xSum / count($collection->list()),
            $ySum / count($collection->list())
        );
    }

    /**
     * Calculate the number of turns until an entity is interacted with
     *
     * @param array $distances
     * @param int $movement
     * @param int $range
     * @return int[]
     */
    public function getTurnsToInteract(array $distances, int $movement, int $range): array
    {
        $turns = [];

        foreach ($distances as $key => $distance) {
            $turns[$key] = (int) ceil(
                ($distance - $range) / $movement
            );
        }

        return $turns;
    }

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
