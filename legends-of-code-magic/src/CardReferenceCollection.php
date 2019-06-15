<?php

namespace CodeInGame\LegendsOfCodeMagic;

class CardReferenceCollection
{
    protected $collection = [];

    public function add(int $instanceId): void
    {
        $this->collection[$instanceId] = $instanceId;
    }

    public function clear(): void
    {
        $this->collection = [];
    }

    public function list(): array
    {
        return $this->collection;
    }

    public function remove(int $instanceId): void
    {
        unset($this->collection[$instanceId]);
    }
}
