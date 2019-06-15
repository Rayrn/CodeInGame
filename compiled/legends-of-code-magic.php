<?php
namespace CodeInGame\LegendsOfCodeMagic {
class Card
{
    private $instanceId;
    private $number;
    private $type;
    private $cost;
    private $attack;
    private $defense;
    private $abilities;
    private $myHealthChange;
    private $opponentHealthChange;
    private $draw;
    public function __construct(int $instanceId, int $number, int $type, int $cost, int $attack, int $defense, string $abilities, int $myHealthChange, int $opponentHealthChange, int $draw)
    {
        $this->instanceId = $instanceId;
        $this->number = $number;
        $this->type = $type;
        $this->cost = $cost;
        $this->attack = $attack;
        $this->defense = $defense;
        $this->abilities = $abilities;
        $this->myHealthChange = $myHealthChange;
        $this->opponentHealthChange = $opponentHealthChange;
        $this->draw = $draw;
    }
    public function getNumber() : int
    {
        return $this->number;
    }
    public function getInstanceId() : int
    {
        return $this->instanceId;
    }
    public function getAttack() : int
    {
        return $this->attack;
    }
    public function getDefense() : int
    {
        return $this->defense;
    }
    public function getCost() : int
    {
        return $this->cost;
    }
}
}

namespace CodeInGame\LegendsOfCodeMagic {
class CardFactory
{
    private $dictionary = [];
    public function addTemplate(int $number, int $type, int $cost, int $attack, int $defense, string $abilities, int $myHealthChange, int $opponentHealthChange, int $draw)
    {
        if (empty($this->dictionary[$number])) {
            $this->dictionary[$number] = [$number, $type, $cost, $attack, $defense, $abilities, $myHealthChange, $opponentHealthChange, $draw];
        }
    }
    public function create(int $number, int $instanceId) : Card
    {
        $template = $this->dictionary[$number];
        return new Card($instanceId, ...$template);
    }
}
}

namespace CodeInGame\LegendsOfCodeMagic {
class CardReferenceCollection
{
    protected $collection = [];
    public function add(int $instanceId) : void
    {
        $this->collection[$instanceId] = $instanceId;
    }
    public function clear() : void
    {
        $this->collection = [];
    }
    public function list() : array
    {
        return $this->collection;
    }
    public function remove(int $instanceId) : void
    {
        unset($this->collection[$instanceId]);
    }
}
}

