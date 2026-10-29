<?php

namespace CodeInGame\FantasticBits\Map;

use CodeInGame\FantasticBits\Map\Entity\EntityCollection;
use CodeInGame\FantasticBits\Map\Interfaces\Identifiable;

class Team implements Identifiable
{
    /**
     * @var Int
     */
    private $id;

    /**
     * @var int
     */
    private $magic;

    /**
     * @var int
     */
    private $score;

    /**
     * @var EntityCollection
     */
    private $wizards;

    public function __construct(int $id, int $magic, int $score)
    {
        $this->id = $id;
        $this->magic = $magic;
        $this->score = $score;
        $this->wizards = new EntityCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMagic(): int
    {
        return $this->magic;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function getWizards(): EntityCollection
    {
        return $this->wizards;
    }

    public function setWizards(EntityCollection $wizards): void
    {
        $this->wizards = $wizards;
    }
}
