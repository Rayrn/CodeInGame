<?php

namespace CodeInGame\CodeVsZombies;

use CodeInGame\CodeVsZombies\Entity\Entity;
use CodeInGame\CodeVsZombies\Entity\EntityCollection;

class Game
{
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
        Entity $ash,
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

    public function updateState(): void
    {
        $this->stateReader->updateState($this->ash, $this->humans, $this->zombies);
    }

    public function getAction(): string
    {
        $ttDie = $this->distanceCalculator->collectionToCollection($this->ash, $this->zombies);
        $ttLive = $this->distanceCalculator->collectionToCollection($this->humans, $this->zombies);
        $ttSave = $this->distanceCalculator->collectionToCollection($this->ash, $this->humans);

        new Debug($ttDie);
        new Debug($ttLive);
        new Debug($ttSave);
    }

    public function cleanup(): void
    {
    }
}
