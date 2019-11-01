<?php

namespace CodeInGame\FantasticBits\Map\Entity;

use CodeInGame\FantasticBits\Location\Position;
use CodeInGame\FantasticBits\Map\Interfaces\Identifiable;
use CodeInGame\FantasticBits\Map\Interfaces\Mappable;
use CodeInGame\FantasticBits\Map\Interfaces\Moveable;

class EntityCollection
{
    /**
     * @var string
     */
    private $entityType;

    /**
     * @var AbstractEntity[]
     */
    private $collection;

    public function get(int $id): ?AbstractEntity
    {
        foreach ($this->collection as $entity) {
            if ($entity->getId() == $id) {
                return $entity;
            }
        }

        return null;
    }

    public function list(): array
    {
        return $this->collection;
    }

    public function set(AbstractEntity ...$collection): void
    {
        $this->collection = [];
        $this->entityType = get_class(reset($collection));

        foreach ($collection as $entity) {
            if ($this->entityType !== get_class($entity)) {
                throw new InvalidArgumentException('A collection may only contain one type of entity');
            }

            $this->collection[$entity->getId()] = $entity;
        }
    }
}
