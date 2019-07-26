<?php

namespace CodeInGame\CodeVsZombies;

use CodeInGame\CodeVsZombies\Entity\Ash;
use CodeInGame\CodeVsZombies\Entity\EntityCollection;
use CodeInGame\CodeVsZombies\Entity\Human;
use CodeInGame\CodeVsZombies\Entity\Position;
use CodeInGame\CodeVsZombies\Entity\Zombie;

class StateReader
{
    /**
     * Update the game state
     *
     * @param Ash $ash
     * @param EntityCollection $humans
     * @param EntityCollection $zombies
     * @return void
     */
    public function updateState(Ash $ash, EntityCollection $humans, EntityCollection $zombies): void
    {
        $this->updateAshPosition($ash);
        $this->updateHumanPositions($humans);
        $this->updateZombiePositions($zombies);
    }

    private function updateAshPosition(Ash $ash): void
    {
        fscanf(STDIN, "%d %d", $x, $y);

        $ash->setPosition(new Position($x, $y));
    }

    private function updateHumanPositions(EntityCollection $humanEntityCollection): void
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
    }

    private function updateZombiePositions(EntityCollection $zombieEntityCollection): void
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
    }
}
