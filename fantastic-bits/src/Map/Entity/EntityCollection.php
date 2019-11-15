<?php

namespace CodeInGame\FantasticBits\Map\Entity;

use ArrayIterator;
use IteratorAggregate;
use CodeInGame\FantasticBits\Location\Position;
use CodeInGame\FantasticBits\Map\Interfaces\Identifiable;
use CodeInGame\FantasticBits\Map\Interfaces\Mappable;
use CodeInGame\FantasticBits\Map\Interfaces\Moveable;

class EntityCollection implements IteratorAggregate
{
    /**
     * @var string
     */
    private $entityType;

    /**
     * @var AbstractEntity[]
     */
    private $collection;

    public function add(AbstractEntity $entity): void
    {
        if ($this->entityType === null) {
            $this->entityType = get_class($entity);
        }
       
        if ($this->entityType !== get_class($entity)) {
            throw new InvalidArgumentException('A collection may only contain one type of entity');
        }

        $this->collection[$entity->getId()] = $entity;
    }

    public function get(int $id): ?AbstractEntity
    {
        foreach ($this->collection as $entity) {
            if ($entity->getId() == $id) {
                return $entity;
            }
        }

        return null;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->collection);
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
