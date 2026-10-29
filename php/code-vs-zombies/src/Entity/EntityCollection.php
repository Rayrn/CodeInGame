<?php

namespace CodeInGame\CodeVsZombies\Entity;

use CodeInGame\CodeVsZombies\Entity\Interfaces\Identifiable;
use CodeInGame\CodeVsZombies\Location\Position;

class EntityCollection
{
    /**
     * @var Entity[]
     */
    private $entities;

    /**
     * @var string
     */
    private $type;

    /**
     * Create a new instance of this object
     *
     * @param string $type Entity collection type
     */
    public function __construct(string $type)
    {
        if (!in_array($type, Entity::VALID_TYPES)) {
            throw new InvalidArgumentException('Invalid type ' . $type);
        }

        $this->type = $type;
        $this->entities = [];
    }

    /**
     * Get the collection type
     *
     * @return strings
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get an Entity from the list
     *
     * @param int $id
     * @return Entity
     */
    public function getEntity(int $id): ?Entity
    {
        foreach ($this->entities as $entity) {
            if ($entity->getId() == $id) {
                return $entity;
            }
        }

        return null;
    }

    /**
     * Add a new entity to the Entity list. Only saves Entities of the same $type as the Entity Collection
     *
     * @param Identifiable|null $entity
     * @return void
     */
    public function addEntity(?Identifiable $entity): void
    {
        if ($entity !== null && $entity->getType() == $this->type) {
            $this->entities[] = $entity;
        }
    }

    /**
     * Overwrite the Entity list. Only saves Entities of the same $type as the Entity Collection
     *
     * @param ...Entity $entities
     * @return void
     */
    public function setEntities(Identifiable ...$entities): void
    {
        $this->entities = [];

        foreach ($entities as $entity) {
            $this->addEntity($entity);
        }
    }

    /**
     * Get a list of Entities
     *
     * @return array
     */
    public function listEntities(): array
    {
        return $this->entities;
    }

    /**
     * Remove an Entity from the list
     *
     * @param int $id
     * @return void
     */
    public function removeEntity(int $id): void
    {
        foreach ($this->entities as $key => $entity) {
            if ($entity->getId() == $id) {
                unset($this->entities[$key]);
            }
        }
    }
}
