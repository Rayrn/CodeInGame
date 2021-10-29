<?php

namespace CodeInGame\SpringChallenge2021\State\Board;

use CodeInGame\SpringChallenge2021\State\Player;

class Tree
{
    /**
     * Location of this tree
     *
     * @var  int
     */
    private $hexId;

    /**
     * Size of this tree: 0-3
     *
     * @var  int
     */
    private $size;

    /**
     * Tree owner
     *
     * @var  Player
     */
    private $owner;

    /**
     * True if Dormant
     *
     * @var  bool
     */
    private $isDormant;

    public function __construct(int $hexId, int $size, Player $player, bool $isDormant)
    {
        $this->hexId = $hexId;
        $this->size = $size;
        $this->owner = $owner;
        $this->isDormat = $isDormant;
    }

    public function getHexId(): int
    {
        return $this->hexId;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getOwner(): Player
    {
        return $this->owner;
    }

    public function getIsDormant(): bool
    {
        return $this->isDormant;
    }

    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    public function setIsDormant(bool $isDormant): void
    {
        $this->isDormant = $isDormant;
    }
}
