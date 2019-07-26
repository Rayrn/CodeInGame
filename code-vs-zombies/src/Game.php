<?php

namespace CodeInGame\CodeVsZombies;

use CodeInGame\CodeVsZombies\Entity\Entity;
use CodeInGame\CodeVsZombies\Entity\EntityCollection;

class Game
{
    private const ASH_MOVEMENT = 1000;
    private const ASH_RANGE = 2000;
    private const ZOMBIE_MOVEMENT = 400;

    /**
     * @var StateReader
     */
    private $stateReader;

    /**
     * @var Entity
     */
    private $ash;

    /**
     * @var EntityCollection
     */
    private $humans;

    /**
     * @var EntityCollection
     */
    private $zombies;

    public function __construct(
        StateReader $stateReader,
        Entity $ash,
        EntityCollection $humans,
        EntityCollection $zombies
    ) {
        $this->stateReader = $stateReader;

        $this->ash = $ash;
        $this->humans = $humans;
        $this->zombies = $zombies;
    }

    public function updateState(): void
    {
        $this->stateReader->updateState($this->ash, $this->humans, $this->zombies);
    }

    public function getAction(): string
    {
    }

    public function cleanup(): void
    {
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
        $x = abs($postiionA->getX() - $postiionB->getX());
        $y = abs($postiionA->gety() - $postiionB->gety());

        return (int) sqrt(($x * $x) + ($y * $y));
    }

    /**
     * Calculate distance in turns between Ash and all zombies
     *
     * @param Ash $ash
     * @param EntityCollection $zombieEntityCollection
     * @return int[]
     */
    private function timeToDie(Ash $ash, EntityCollection $zombieEntityCollection): array
    {
        $zombies = [];
        foreach ($zombieEntityCollection->list() as $zombie) {
            // Turns
            $distance = $this->getDistance($ash->getPosition(), $zombie->getPosition()) / 1000;

            $zombies[$zombie->getId()] = intval($distance) - (self::ASH_RANGE / self::ASH_MOVEMENT);
        }

        return $zombies;
    }

    private function timeToLive()
    {
    }
}
