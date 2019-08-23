<?php

namespace CodeInGame\CodeVsZombies;

use CodeInGame\CodeVsZombies\Entity\Ash;
use CodeInGame\CodeVsZombies\Entity\Entity;
use CodeInGame\CodeVsZombies\Entity\EntityCollection;
use CodeInGame\CodeVsZombies\Entity\Position;
use CodeInGame\CodeVsZombies\Helper\DistanceCalculator;

class Game
{
    private const ASH_MOVEMENT = 1000;
    private const ASH_RANGE = 2000;
    private const ZOMBIE_MOVEMENT = 400;
    private const ZOMBIE_RANGE = 0;

    /**
     * @var Entity
     */
    private $ash;

    /**
     * @var DistanceCalculator
     */
    private $distanceCalculator;

    /**
     * @var EntityCollection
     */
    private $humans;

    /**
     * @var StateReader
     */
    private $stateReader;

    /**
     * @var EntityCollection
     */
    private $zombies;

    public function __construct(
        StateReader $stateReader,
        Ash $ash,
        EntityCollection $humans,
        EntityCollection $zombies,
        DistanceCalculator $distanceCalculator
    ) {
        $this->stateReader = $stateReader;

        $this->ash = $ash;
        $this->humans = $humans;
        $this->zombies = $zombies;

        $this->distanceCalculator = new DistanceCalculator();
    }

    /**
     * Update the game state
     *
     * @return void
     */
    public function updateState(): void
    {
        $this->stateReader->updateState($this->ash, $this->humans, $this->zombies);
    }

    /**
     * Get the next target position
     *
     * @return Position
     */
    public function getAction(): Position
    {
        if (count($this->zombies->list()) == 1) {
            return $this->getFirstEntityPosition($this->zombies);
        }

        if (count($this->humans->list()) == 1) {
            return $this->getFirstEntityPosition($this->humans);
        }

        $priorityList = $this->getPriority();

        $hitList = $this->distanceCalculator->getTurnsToInteract(
            $this->distanceCalculator->ashToCollection($this->ash, $this->zombies),
            self::ASH_MOVEMENT,
            self::ASH_RANGE
        );

        if (min($priorityList) > min($hitList)) {
            return $this->getTargetFromList($hitList, $this->zombies);
        }
        
        return $this->getTargetFromList($priorityList, $this->humans);
    }

    /**
     * Retreive the position of the first entity in the collection
     *
     * @param EntityCollection $collection
     * @return Position
     */
    private function getFirstEntityPosition(EntityCollection $collection): Position
    {
        $entities = $collection->list();

        return reset($entities)->getPosition();
    }

    private function getPriority(): array
    {
        $timeToLive = $this->distanceCalculator->getTurnsToInteract(
            $this->distanceCalculator->collectionToCollection($this->humans, $this->zombies),
            self::ZOMBIE_MOVEMENT,
            self::ZOMBIE_RANGE
        );

        $timeToSave = $this->distanceCalculator->getTurnsToInteract(
            $this->distanceCalculator->ashToCollection($this->ash, $this->humans),
            self::ASH_MOVEMENT,
            self::ASH_RANGE
        );

        // Filter out the walking dead
        $priorityList = [];
        foreach ($this->humans->list() as $human) {
            $id = $human->getId();

            $ttl = $timeToLive[$id] ?? -1;
            $tts = $timeToSave[$id] ?? -1;

            if ($ttl >= $tts && $tts >= 0) {
                $priorityList[$id] = $ttl - $tts;
            }
        }

        asort($priorityList);

        return $priorityList;
    }

    private function getTargetFromList(array $priorityList, EntityCollection $targets): Position
    {
        $min = min($priorityList);
        $idSet = array_filter($priorityList, function ($priority) use ($min) {
            return $min == $priority;
        });

        asort($idSet);

        new Debug($targets->getType(), $idSet);

        return $targets->get(array_key_first($idSet))->getPosition();
    }
}
