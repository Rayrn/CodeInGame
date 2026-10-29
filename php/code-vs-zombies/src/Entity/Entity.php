<?php

namespace CodeInGame\CodeVsZombies\Entity;

use CodeInGame\CodeVsZombies\Entity\Interfaces\Identifiable;
use CodeInGame\CodeVsZombies\Entity\Interfaces\Mappable;
use CodeInGame\CodeVsZombies\Entity\Interfaces\Sociable;
use CodeInGame\CodeVsZombies\Location\DistanceCalculator;
use CodeInGame\CodeVsZombies\Location\Position;

abstract class Entity implements Identifiable, Mappable, Sociable
{
    /**
     * List of valid Entity types
     */
    public const VALID_TYPES = [self::HUMAN, self::ZOMBIE];
    public const HUMAN = 'human';
    public const ZOMBIE = 'zombie';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Position
     */
    protected $position;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var EntityCollection
     */
    protected $friendList;

    /**
     * @var array
     */
    protected $friendDistance;

    /**
     * @var EntityCollection
     */
    protected $enemyList;

    /**
     * @var array
     */
    protected $enemyDistance;

    /**
     * Create a new instance of this entity
     *
     * @param string $type
     * @param int $id
     */
    public function __construct(string $type, int $id)
    {
        if (!in_array($type, self::VALID_TYPES)) {
            throw new InvalidArgumentException('Invalid type ' . $type);
        }

        $this->id = $id;
        $this->type = $type;
    }

    /**
     * Get the Entity ID
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the Entity type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the Position for the Entity
     *
     * @param Position $postition
     * @return void
     */
    public function setPosition(Position $position): void
    {
        $this->position = $position;
    }

    /**
     * Get the Entity Position
     *
     * @return Position
     */
    public function getPosition(): Position
    {
        return $this->position;
    }

    /**
     * Calculate the distance between this entity and all other entites of the same type
     *
     * @param EntityCollection $collection
     * @return void
     * @throws InvalidArgumentException
     */
    public function lookForFriends(EntityCollection $collection): void
    {
        if ($collection->getType() !== $this->type) {
            throw new InvalidArgumentException("With friends like these... (entity: {$collection->getType()})");
        }

        $this->friendList = $collection;
        $this->friendDistance = (new DistanceCalculator())->mappableToCollection($this, $collection);
    }

    /**
     * Return a collection containing all friends within a certain distance
     *
     * @param int $targetDistance
     * @return array
     * @throws Exception
     */
    public function listFriendsInRange(int $targetDistance): EntityCollection
    {
        if ($this->friendList === null) {
            throw new Exception('You should probably try looking for friends before asking who is nearby...');
        }

        return $this->filterColletionByDistance($this->friendList, $this->friendDistance, $targetDistance);
    }

    /**
     * Calculate the distance between this entity and all other entites of the opposite type
     *
     * @param EntityCollection $collection
     * @return void
     * @throws InvalidArgumentException
     */
    public function lookForEnemies(EntityCollection $collection): void
    {
        if ($collection->getType() === $this->type) {
            throw new InvalidArgumentException("These are your friends? (entity: {$collection->getType()})");
        }

        $this->enemyList = $collection;
        $this->enemyDistance = (new DistanceCalculator())->mappableToCollection($this, $collection);
    }

    /**
     * Return a collection containing all enemies within a certain distance
     *
     * @param int $targetDistance
     * @return EntityCollection
     * @throws Exception
     */
    public function listEnemiesInRange(int $targetDistance): EntityCollection
    {
        if ($this->friendList === null) {
            throw new Exception('If you think you\'re safe, try opening the curtains...');
        }

        return $this->filterColletionByDistance($this->enemyList, $this->enemyDistance, $targetDistance);
    }

    /**
     * Return a new collection containing all entities within the target distance
     *
     * @param EntityCollection $collection
     * @param array $distances
     * @param int $targetDistance
     * @return EntityCollection
     */
    protected function filterColletionByDistance(
        EntityCollection $collection,
        array $distances,
        int $targetDistance
    ): EntityCollection {
        $nearby = new EntityCollection($collection->getType());

        foreach ($distances as $id => $distance) {
            if ($distance > $distance) {
                continue;
            }

            $nearby->addEntity($collection->getEntity($id));
        }

        return $nearby;
    }
}
