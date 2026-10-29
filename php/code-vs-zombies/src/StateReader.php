<?php

namespace CodeInGame\CodeVsZombies;

use CodeInGame\CodeVsZombies\Entity\Ash;
use CodeInGame\CodeVsZombies\Entity\EntityCollection;
use CodeInGame\CodeVsZombies\Entity\Human;
use CodeInGame\CodeVsZombies\Entity\Zombie;
use CodeInGame\CodeVsZombies\Location\Position;

class StateReader
{
    /**
     * Update the game state
     *
     * @param Ash $ash
     * @param EntityCollection $humans
     * @param EntityCollection $zombies
     * @return array
     */
    public function updateState(Ash $ash, EntityCollection $humans, EntityCollection $zombies): array
    {
        $ash = $this->updateAshPosition($ash);
        $humans = $this->updateHumanPositions($humans);
        $zombies = $this->updateZombiePositions($zombies);

        return [$ash, $humans, $zombies];
    }

    private function updateAshPosition(Ash $ash): Ash
    {
        fscanf(STDIN, "%d %d", $x, $y);

        $ash->setPosition(new Position($x, $y));

        return $ash;
    }

    private function updateHumanPositions(EntityCollection $humanEntityCollection): EntityCollection
    {
        fscanf(STDIN, "%d", $humanCount);

        $humans = [];
        for ($i = 0; $i < $humanCount; $i++) {
            fscanf(STDIN, "%d %d %d", $humanId, $humanX, $humanY);

            $human = new Human($humanId);
            $human->setPosition(new Position($humanX, $humanY));

            $humans[] = $human;
        }

        $humanEntityCollection->setEntities(...$humans);

        return $humanEntityCollection;
    }

    private function updateZombiePositions(EntityCollection $zombieEntityCollection): EntityCollection
    {
        fscanf(STDIN, "%d", $zombieCount);

        $zombies = [];
        for ($i = 0; $i < $zombieCount; $i++) {
            fscanf(STDIN, "%d %d %d %d %d", $zombieId, $zombieX, $zombieY, $zombieXNext, $zombieYNext);

            $zombie = new Zombie($zombieId);
            $zombie->setPosition(new Position($zombieX, $zombieY));
            $zombie->setNextPosition(new Position($zombieXNext, $zombieYNext));

            $zombies[] = $zombie;
        }

        $zombieEntityCollection->setEntities(...$zombies);

        return $zombieEntityCollection;
    }
}
