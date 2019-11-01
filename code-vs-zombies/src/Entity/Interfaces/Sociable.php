<?php

namespace CodeInGame\CodeVsZombies\Entity\Interfaces;

use CodeInGame\CodeVsZombies\Entity\EntityCollection;

interface Sociable
{
    /**
     * Calculate the distance between this entity and all other entites of the same type
     *
     * @param EntityCollection $collection
     * @return void
     * @throws InvalidArgumentException
     */
    public function lookForFriends(EntityCollection $collection): void;

    /**
     * Return a collection containing all friends within a certain distance
     *
     * @param int $targetDistance
     * @return EntityCollection
     * @throws Exception
     */
    public function listFriendsInRange(int $targetDistance): EntityCollection;

    /**
     * Calculate the distance between this entity and all other entites of the opposite type
     *
     * @param EntityCollection $collection
     * @return void
     * @throws InvalidArgumentException
     */
    public function lookForEnemies(EntityCollection $collection): void;

    /**
     * Return a collection containing all enemies within a certain distance
     *
     * @param int $targetDistance
     * @return EntityCollection
     * @throws Exception
     */
    public function listEnemiesInRange(int $targetDistance): EntityCollection;
}
