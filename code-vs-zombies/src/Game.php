<?php

namespace CodeInGame\CodeVsZombies;

use CodeInGame\CodeVsZombies\Entity\Ash;
use CodeInGame\CodeVsZombies\Entity\Entity;
use CodeInGame\CodeVsZombies\Entity\EntityCollection;
use CodeInGame\CodeVsZombies\Location\DistanceCalculator;
use CodeInGame\CodeVsZombies\Location\Position;

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

        $this->distanceCalculator = $distanceCalculator;
    }

    /**
     * Update the game state
     *
     * @return void
     */
    public function updateState(): void
    {
        $response = $this->stateReader->updateState($this->ash, $this->humans, $this->zombies);

        [$this->ash, $this->humans, $this->zombies] = $response;
    }

    /**
     * Get the next target position
     *
     * @return Position
     */
    public function getAction(): Position
    {
        if (count($this->zombies->listEntities()) == 1) {
            return $this->getFirstEntityPosition($this->zombies);
        }

        if (count($this->humans->listEntities()) == 1) {
            return $this->getFirstEntityPosition($this->humans);
        }

        $turnsToAct = $this->getTurnsToAct();

        if (min($turnsToAct) == 0) {
            return $this->getTargetFromList($turnsToAct, $this->humans);
        }

        $turnsToKill = $this->distanceCalculator->getTurnsToInteract(
            $this->distanceCalculator->mappableToCollection($this->ash, $this->zombies),
            self::ASH_MOVEMENT,
            self::ASH_RANGE
        );

        return $this->getTargetFromList($turnsToKill, $this->zombies);
    }

    /**
     * Retreive the position of the first entity in the collection
     *
     * @param EntityCollection $collection
     * @return Position
     */
    private function getFirstEntityPosition(EntityCollection $collection): Position
    {
        $entities = $collection->listEntities();

        return reset($entities)->getPosition();
    }

    /**
     * Calculate the number of turns until Ash has to run and save people
     *
     * @return array
     */
    private function getTurnsToAct(): array
    {
        $timeToLive = $this->distanceCalculator->getTurnsToInteract(
            $this->distanceCalculator->collectionToCollection($this->humans, $this->zombies),
            self::ZOMBIE_MOVEMENT,
            self::ZOMBIE_RANGE
        );

        $timeToSave = $this->distanceCalculator->getTurnsToInteract(
            $this->distanceCalculator->mappableToCollection($this->ash, $this->humans),
            self::ASH_MOVEMENT,
            self::ASH_RANGE
        );

        // Filter out the walking dead and calculate time to act
        $priorityList = [];
        foreach ($this->humans->listEntities() as $human) {
            $id = $human->getId();

            $ttl = $timeToLive[$id] ?? -1;
            $tts = $timeToSave[$id] ?? -1;

            if ($ttl >= $tts && $tts >= 0) {
                $priorityList[$id] = $ttl - $tts;
            }
        }

        // Sort whilst preserving the keys (Entity ID)
        asort($priorityList);

        return $priorityList;
    }

    /**
     * Get the highest priority target from a list of entities
     *
     * @param array $list
     * @param EntityCollection $targets
     * @return Position
     */
    private function getTargetFromList(array $list, EntityCollection $targets): Position
    {
        $min = min($list);

        $idSet = array_filter($list, function ($priority) use ($min) {
            return $min == $priority;
        });

        asort($idSet);

        return $targets->getEntity(array_key_first($idSet))->getPosition();
    }
}
