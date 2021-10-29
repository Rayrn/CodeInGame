<?php

namespace CodeInGame\SpringChallenge2021;

use CodeInGame\SpringChallenge2021\State\Board;
use CodeInGame\SpringChallenge2021\State\Board\Hex;
use CodeInGame\SpringChallenge2021\State\Board\Tree;
use CodeInGame\SpringChallenge2021\State\Player;

class Game
{
    /** @var Board */
    private $board;

    /** @var string[] */
    private $actions = [];

    /** @var Hex[] */
    private $hexes = [];

    /** @var Tree[] */
    private $trees = [];

    public function __construct(Board $board)
    {
        $this->board = $board;
    }

    public function getBoard(): Board
    {
        return $this->board;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function getHexes(): array
    {
        return $this->hexes;
    }

    public function getTrees(): array
    {
        return $this->trees;
    }

    public function setActions(string ...$actions): void
    {
        $this->actions = $actions;
    }

    public function setHexes(Hex ...$hexes): void
    {
        $this->hexes = $hexes;
    }

    public function setTrees(Tree ...$trees): void
    {
        $this->trees = $trees;
    }
}
