<?php
namespace CodeInGame\CodeVsZombies {
class Debug
{
    public function __construct(...$entity)
    {
        foreach ($entity as $output) {
            error_log(print_r($output, true));
        }
    }
}
}

namespace CodeInGame\CodeVsZombies\Entity {
use CodeInGame\CodeVsZombies\Entity\Interfaces\Mappable;
use CodeInGame\CodeVsZombies\Location\Position;
class Ash implements Mappable
{
    /**
     * @var Position
     */
    protected $position;
    /**
     * Set the Position for the Entity
     *
     * @param Position $postition
     * @return void
     */
    public function setPosition(Position $position) : void
    {
        $this->position = $position;
    }
    /**
     * Get the Entity Position
     *
     * @return Position
     */
    public function getPosition() : Position
    {
        return $this->position;
    }
}
}

namespace CodeInGame\CodeVsZombies\Entity {
use CodeInGame\CodeVsZombies\Entity\Interfaces\Identifiable;
use CodeInGame\CodeVsZombies\Entity\Interfaces\Mappable;
use CodeInGame\CodeVsZombies\Location\Position;
abstract class Entity implements Identifiable, Mappable
{
    /**
     * List of valid Entity types
     */
    public const VALID_TYPES = [self::HUMAN, self::ZOMBIE];
    public const HUMAN = 'human';
    public const ZOMBIE = 'zombie';
    /**
     * @var int
     */
    protected $id;
    /**
     * @var Position
     */
    protected $position;
    /**
     * @var string
     */
    protected $type;
    public function __construct(string $type, int $id)
    {
        if (!in_array($type, self::VALID_TYPES)) {
            throw new InvalidArgumentException('Invalid type ' . $type);
        }
        $this->id = $id;
        $this->type = $type;
    }
    /**
     * Get the Entity ID
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }
    /**
     * Get the Entity type
     *
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }
    /**
     * Set the Position for the Entity
     *
     * @param Position $postition
     * @return void
     */
    public function setPosition(Position $position) : void
    {
        $this->position = $position;
    }
    /**
     * Get the Entity Position
     *
     * @return Position
     */
    public function getPosition() : Position
    {
        return $this->position;
    }
}
}

namespace CodeInGame\CodeVsZombies\Entity {
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
     * Overwrite the Entity list. Only saves Entities of the same $type as the Entity Collection
     *
     * @param ...Entity $entities
     * @return void
     */
    public function setEntities(Identifiable ...$entities) : void
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
    public function get(int $id) : ?Entity
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
    public function getType() : string
    {
        return $this->type;
    }
    /**
     * Get a list of Entities
     *
     * @return array
     */
    public function list() : array
    {
        return $this->entities;
    }
    /**
     * Remove an Entity from the list
     *
     * @param int $id
     * @return void
     */
    public function remove(int $id) : void
    {
        foreach ($this->entities as $key => $entity) {
            if ($entity->getId() == $id) {
                unset($this->entities[$key]);
            }
        }
    }
}
}

namespace CodeInGame\CodeVsZombies\Entity {
class Human extends Entity
{
    /**
     * Create a new Human Entity
     *
     * @param int $id;
     */
    public function __construct(int $id)
    {
        parent::__construct(self::HUMAN, $id);
    }
}
}

namespace CodeInGame\CodeVsZombies\Entity\Interfaces {
interface Identifiable
{
    /**
     * Get the Entity ID
     *
     * @return int
     */
    public function getId() : int;
    /**
     * Get the Entity type
     *
     * @return string
     */
    public function getType() : string;
}
}

namespace CodeInGame\CodeVsZombies\Entity\Interfaces {
use CodeInGame\CodeVsZombies\Location\Position;
interface Mappable
{
    /**
     * Set the Position for the Entity
     *
     * @param Position $postition
     * @return void
     */
    public function setPosition(Position $position) : void;
    /**
     * Get the Entity Position
     *
     * @return Position
     */
    public function getPosition() : Position;
}
}

namespace CodeInGame\CodeVsZombies\Entity\Interfaces {
use CodeInGame\CodeVsZombies\Location\Position;
interface Moveable
{
    /**
     * Set the next Position for the Entity
     *
     * @param Position $postition
     * @return void
     */
    public function setNextPosition(Position $position) : void;
    /**
     * Get the next Entity Position
     *
     * @return Position
     */
    public function getNextPosition() : Position;
}
}

namespace CodeInGame\CodeVsZombies\Entity {
use CodeInGame\CodeVsZombies\Entity\Interfaces\Moveable;
use CodeInGame\CodeVsZombies\Location\Position;
class Zombie extends Entity implements Moveable
{
    /**
     * @var int
     */
    private $nextPosition;
    /**
     * Create a new Zombie Entity
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct(self::ZOMBIE, $id);
    }
    /**
     * Set the next Position for the Entity
     *
     * @param Position $postition
     * @return void
     */
    public function setNextPosition(Position $position) : void
    {
        $this->nextPosition = $position;
    }
    /**
     * Get the next Entity Position
     *
     * @return Position
     */
    public function getNextPosition() : Position
    {
        return $this->nextPosition;
    }
}
}

namespace CodeInGame\CodeVsZombies {
use CodeInGame\CodeVsZombies\Entity\Ash;
use CodeInGame\CodeVsZombies\Entity\Entity;
use CodeInGame\CodeVsZombies\Entity\EntityCollection;
use CodeInGame\CodeVsZombies\Location\DistanceCalculator;
use CodeInGame\CodeVsZombies\Location\Position;
class Game
{
    private const ASH_MOVEMENT = 1000;
    private const ASH_RANGE = 2000;
    private const ZOMBIE_MOVEMENT = 400;
    private const ZOMBIE_RANGE = 0;
    /**
     * @var Entity
     */
    private $ash;
    /**
     * @var DistanceCalculator
     */
    private $distanceCalculator;
    /**
     * @var EntityCollection
     */
    private $humans;
    /**
     * @var StateReader
     */
    private $stateReader;
    /**
     * @var EntityCollection
     */
    private $zombies;
    public function __construct(StateReader $stateReader, Ash $ash, EntityCollection $humans, EntityCollection $zombies, DistanceCalculator $distanceCalculator)
    {
        $this->stateReader = $stateReader;
        $this->ash = $ash;
        $this->humans = $humans;
        $this->zombies = $zombies;
        $this->distanceCalculator = new DistanceCalculator();
    }
    /**
     * Update the game state
     *
     * @return void
     */
    public function updateState() : void
    {
        $this->stateReader->updateState($this->ash, $this->humans, $this->zombies);
    }
    /**
     * Get the next target position
     *
     * @return Position
     */
    public function getAction() : Position
    {
        if (count($this->zombies->list()) == 1) {
            return $this->getFirstEntityPosition($this->zombies);
        }
        if (count($this->humans->list()) == 1) {
            return $this->getFirstEntityPosition($this->humans);
        }
        $priorityList = $this->getPriority();
        $hitList = $this->distanceCalculator->getTurnsToInteract($this->distanceCalculator->ashToCollection($this->ash, $this->zombies), self::ASH_MOVEMENT, self::ASH_RANGE);
        if (min($priorityList) > min($hitList)) {
            return $this->getTargetFromList($hitList, $this->zombies);
        }
        return $this->getTargetFromList($priorityList, $this->humans);
    }
    /**
     * Retreive the position of the first entity in the collection
     *
     * @param EntityCollection $collection
     * @return Position
     */
    private function getFirstEntityPosition(EntityCollection $collection) : Position
    {
        $entities = $collection->list();
        return reset($entities)->getPosition();
    }
    private function getPriority() : array
    {
        $timeToLive = $this->distanceCalculator->getTurnsToInteract($this->distanceCalculator->collectionToCollection($this->humans, $this->zombies), self::ZOMBIE_MOVEMENT, self::ZOMBIE_RANGE);
        $timeToSave = $this->distanceCalculator->getTurnsToInteract($this->distanceCalculator->ashToCollection($this->ash, $this->humans), self::ASH_MOVEMENT, self::ASH_RANGE);
        // Filter out the walking dead
        $priorityList = [];
        foreach ($this->humans->list() as $human) {
            $id = $human->getId();
            $ttl = $timeToLive[$id] ?? -1;
            $tts = $timeToSave[$id] ?? -1;
            if ($ttl >= $tts && $tts >= 0) {
                $priorityList[$id] = $ttl - $tts;
            }
        }
        asort($priorityList);
        return $priorityList;
    }
    private function getTargetFromList(array $list, EntityCollection $targets) : Position
    {
        $min = min($list);
        $idSet = array_filter($list, function ($priority) use($min) {
            return $min == $priority;
        });
        asort($idSet);
        // new Debug($targets->getType(), $idSet);
        return $targets->get(array_key_first($idSet))->getPosition();
    }
}
}

namespace CodeInGame\CodeVsZombies\Location {
use CodeInGame\CodeVsZombies\Debug;
use CodeInGame\CodeVsZombies\Entity\Ash;
use CodeInGame\CodeVsZombies\Entity\Entity;
use CodeInGame\CodeVsZombies\Entity\EntityCollection;
class DistanceCalculator
{
    /**
     * Calculate distance between Ash and a set of Entities
     *
     * @param Ash $ash
     * @param EntityCollection $collection
     * @return int[]
     */
    public function ashToCollection(Ash $ash, EntityCollection $collection) : array
    {
        $entites = [];
        foreach ($collection->list() as $entity) {
            $entites[$entity->getId()] = intval($this->getDistance($ash->getPosition(), $entity->getPosition()));
        }
        return $entites;
    }
    /**
     * Calculate distance in turns between two sets of Entities (nearest only)
     *
     * @param EntityCollection $collectionA
     * @param EntityCollection $collectionB
     * @return int[]
     */
    public function collectionToCollection(EntityCollection $collectionA, EntityCollection $collectionB) : array
    {
        $entites = [];
        foreach ($collectionA->list() as $entity) {
            $nearest = $this->getNearestEntity($entity->getPosition(), $collectionB);
            if (is_null($nearest)) {
                // If null is returned then the second entity collection was empty. Skip.
                break;
            }
            $entites[$entity->getId()] = intval($this->getDistance($entity->getPosition(), $nearest->getPosition()));
        }
        return $entites;
    }
    /**
     * Calculate the number of turns until an entity is interacted with
     *
     * @param array $distances
     * @param int $movement
     * @param int $range
     * @return int[]
     */
    public function getTurnsToInteract(array $distances, int $movement, int $range) : array
    {
        $turns = [];
        foreach ($distances as $key => $distance) {
            $turns[$key] = (int) ceil(($distance - $range) / $movement);
        }
        return $turns;
    }
    /**
     * Get the distance between two positions
     *
     * @param Position $positionA
     * @param Position $positionB
     * @return int
     */
    private function getDistance(Position $positionA, Position $positionB) : int
    {
        $x = abs($positionA->getX() - $positionB->getX());
        $y = abs($positionA->gety() - $positionB->gety());
        return (int) sqrt($x * $x + $y * $y);
    }
    /**
     * Get the nearest entity to a position
     *
     * @param Position $position
     * @param EntityCollection $collection
     * @return ?Entity
     */
    private function getNearestEntity(Position $position, EntityCollection $collection) : ?Entity
    {
        $minDistance = null;
        $nearest = null;
        foreach ($collection->list() as $entity) {
            $distance = $this->getDistance($position, $entity->getPosition());
            if ($distance < $minDistance || is_null($minDistance)) {
                $minDistance = $distance;
                $nearest = $entity;
            }
        }
        return $nearest;
    }
}
}

namespace CodeInGame\CodeVsZombies\Location {
class Position
{
    /**
     * @var int
     */
    private $x;
    /**
     * @var int
     */
    private $y;
    /**
     * Create a new Position
     *
     * @param int $id;
     */
    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }
    /**
     * Outputs a representation of the object as a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->x . ' ' . $this->y;
    }
    /**
     * Get the X position
     *
     * @return int
     */
    public function getX() : int
    {
        return $this->x;
    }
    /**
     * Get the Y position
     *
     * @return int
     */
    public function getY() : int
    {
        return $this->y;
    }
}
}

namespace CodeInGame\CodeVsZombies {
use CodeInGame\CodeVsZombies\Entity\Ash;
use CodeInGame\CodeVsZombies\Entity\Entity;
use CodeInGame\CodeVsZombies\Entity\EntityCollection;
use CodeInGame\CodeVsZombies\Location\DistanceCalculator;
$game = new Game(new StateReader(), new Ash(), new EntityCollection(Entity::HUMAN), new EntityCollection(Entity::ZOMBIE), new DistanceCalculator());
// game loop
while (true) {
    $game->updateState();
    echo $game->getAction() . PHP_EOL;
}
}

namespace CodeInGame\CodeVsZombies {
use CodeInGame\CodeVsZombies\Entity\Ash;
use CodeInGame\CodeVsZombies\Entity\EntityCollection;
use CodeInGame\CodeVsZombies\Entity\Human;
use CodeInGame\CodeVsZombies\Entity\Zombie;
use CodeInGame\CodeVsZombies\Location\Position;
class StateReader
{
    /**
     * Update the game state
     *
     * @param Ash $ash
     * @param EntityCollection $humans
     * @param EntityCollection $zombies
     * @return void
     */
    public function updateState(Ash $ash, EntityCollection $humans, EntityCollection $zombies) : void
    {
        $this->updateAshPosition($ash);
        $this->updateHumanPositions($humans);
        $this->updateZombiePositions($zombies);
    }
    private function updateAshPosition(Ash $ash) : void
    {
        fscanf(STDIN, "%d %d", $x, $y);
        $ash->setPosition(new Position($x, $y));
    }
    private function updateHumanPositions(EntityCollection $humanEntityCollection) : void
    {
        fscanf(STDIN, "%d", $humanCount);
        $humans = [];
        for ($i = 0; $i < $humanCount; $i++) {
            fscanf(STDIN, "%d %d %d", $humanId, $humanX, $humanY);
            $human = new Human($humanId);
            $human->setPosition(new Position($humanX, $humanY));
            $humans[] = $human;
        }
        $humanEntityCollection->setEntities(...$humans);
    }
    private function updateZombiePositions(EntityCollection $zombieEntityCollection) : void
    {
        fscanf(STDIN, "%d", $zombieCount);
        $zombies = [];
        for ($i = 0; $i < $zombieCount; $i++) {
            fscanf(STDIN, "%d %d %d %d %d", $zombieId, $zombieX, $zombieY, $zombieXNext, $zombieYNext);
            $zombie = new Zombie($zombieId);
            $zombie->setPosition(new Position($zombieX, $zombieY));
            $zombie->setNextPosition(new Position($zombieXNext, $zombieYNext));
            $zombies[] = $zombie;
        }
        $zombieEntityCollection->setEntities(...$zombies);
    }
}
}

