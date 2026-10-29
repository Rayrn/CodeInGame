<?php

namespace CodeInGame\SpringChallenge2021\State\Board;

class Hex
{
    /**
     * 0 is the center cell, the next cells spiral outwards
     *
     * @var int
     */
    private $id;

    /**
     * 0 if the cell is unusable, 1-3 for usable cells
     *
     * @var int
     */
    private $richness;

    /**
     * The index of the neighbouring cell for each direction
     *
     * @var array
     */
    private $neighbours = [];

    public function __construct(int $id, int $richness)
    {
        $this->id = $id;
        $this->richness = $richness;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRichness(): int
    {
        return $this->richness;
    }

    public function getNeighbours(): array
    {
        return $this->neighbours;
    }

    public function setRichness(int $richness): void
    {
        $this->richness = $richness;
    }

    public function setNeighbours(Hex ...$neighbours): void
    {
        $this->neigbours = $neighbours;
    }
}
