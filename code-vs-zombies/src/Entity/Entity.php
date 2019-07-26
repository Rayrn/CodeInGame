<?php

namespace CodeInGame\CodeVsZombies\Entity;

abstract class Entity implements Identifiable, Mappable
{
    /**
     * List of valid Entity types
     */
    public const VALID_TYPES = [self::HUMAN, self::ZOMBIE];
    public const HUMAN = 'human';
    public const ZOMBIE = 'zombie';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Position
     */
    protected $position;

    public function __construct(string $type, int $id)
    {
        if (!in_array($type, self::VALID_TYPES)) {
            throw new InvalidArgumentException('Invalid type ' . $type);
        }

        $this->id = $id;
        $this->type = $type;
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
     * Get the Entity ID
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
}
