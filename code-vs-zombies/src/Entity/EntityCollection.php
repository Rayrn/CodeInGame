<?php

namespace CodeInGame\CodeVsZombies\Entity;

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
     * Overwrite the Entity list. Only saves Entities of the same $type as the Entity Collection
     *
     * @param ...Entity $entities
     * @return void
     */
    public function setEntities(Identifiable ...$entities): void
    {
        $this->entities = [];

        foreach ($entities as $entity) {
            if ($entity->getType() !== $this->type) {
                continue;
            }

            $this->entities[] = $entity;
        }
    }

    /**
     * Get an Entity from the list
     *
     * @param int $id
     * @return Entity
     */
    public function get(int $id): ?Entity
    {
        foreach ($this->entities as $entity) {
            if ($entity->getId() == $id) {
                return $entity;
            }
        }

        return null;
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
     * Get a list of Entities
     *
     * @return array
     */
    public function list(): array
    {
        return $this->entities;
    }

    /**
     * Remove an Entity from the list
     *
     * @param int $id
     * @return void
     */
    public function remove(int $id): void
    {
        foreach ($this->entities as $key => $entity) {
            if ($entity->getId() == $id) {
                unset($this->entities[$key]);
            }
        }
    }
}
