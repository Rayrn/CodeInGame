<?php

namespace CodeInGame\CodeVsZombies;

use CodeInGame\CodeVsZombies\Entity\Ash;
use CodeInGame\CodeVsZombies\Entity\Entity;
use CodeInGame\CodeVsZombies\Entity\EntityCollection;
use CodeInGame\CodeVsZombies\Helper\DistanceCalculator;

class Game
{
    private const ASH_MOVEMENT = 1000;
    private const ASH_RANGE = 2000;
    private const ZOMBIE_MOVEMENT = 400;

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

    public function updateState(): void
    {
        $this->stateReader->updateState($this->ash, $this->humans, $this->zombies);
    }

    public function getAction(): string
    {
        $ttDie = $this->distanceCalculator->ashToCollection($this->ash, $this->zombies);
        $ttLive = $this->distanceCalculator->collectionToCollection($this->humans, $this->zombies);
        $ttSave = $this->distanceCalculator->ashToCollection($this->ash, $this->humans);

        new Debug($ttDie);
        new Debug($ttLive);
        new Debug($ttSave);

        return '';
    }

    public function cleanup(): void
    {
    }
}
