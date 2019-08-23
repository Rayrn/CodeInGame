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
            throw new InvalidArgumentException("With friends like these... (Wrong entity: {$collection->getType()})");
        }

        // Save friends list
        $this->friendList = $collection;

        // Calculate distance
        $this->friendDistance = (new DistanceCalculator())->mappableToCollection($this, $collection);
    }

    /**
     * Return a collection containing all friends who are close enough
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

        $nearby = new EntityCollection($this->type);

        foreach ($this->friendDistance as $id => $distance) {
            if ($distance > $distance) {
                continue;
            }

            $nearby->addEntity($this->friendList->getEntity($id));
        }

        return $nearby;
    }
}
