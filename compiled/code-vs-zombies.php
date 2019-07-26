<?php
namespace CodeInGame\CodeVsZombies {
class Debug
{
    public function __construct($entity)
    {
        error_log(var_export($entity, true));
    }
}
}

namespace CodeInGame\CodeVsZombies\Entity {
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
abstract class Entity implements Identifiable, Mappable
{
    /**
     * List of valid Entity types
     */
    public const VALID_TYPES = [self::HUMAN, self::ZOMBIE];
    public const HUMAN = 'human';
    public const ZOMBIE = 'zombie';
    /**
     * @var string
     */
    protected $type;
    /**
     * @var int
     */
    protected $id;
    /**
     * @var Position
     */
    protected $position;
    public function __construct(string $type, int $id)
    {
        if (!in_array($type, self::VALID_TYPES)) {
            throw new InvalidArgumentException('Invalid type ' . $type);
        }
        $this->id = $id;
        $this->type = $type;
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
     * Get the Entity ID
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
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
     * Get a list of Entities
     *
     * @return array
     */
    public function list() : array
    {
        return $this->entities;
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

namespace CodeInGame\CodeVsZombies\Entity {
interface Identifiable
{
    /**
     * Get the Entity ID
     *
     * @return int
     */
    public function getId() : int;
}
}

namespace CodeInGame\CodeVsZombies\Entity {
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

namespace CodeInGame\CodeVsZombies\Entity {
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

namespace CodeInGame\CodeVsZombies\Entity {
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
use CodeInGame\CodeVsZombies\Entity\Entity;
use CodeInGame\CodeVsZombies\Entity\EntityCollection;
class Game
{
    private const ASH_MOVEMENT = 1000;
    private const ASH_RANGE = 2000;
    private const ZOMBIE_MOVEMENT = 400;
    /**
     * @var StateReader
     */
    private $stateReader;
    /**
     * @var Entity
     */
    private $ash;
    /**
     * @var EntityCollection
     */
    private $humans;
    /**
     * @var EntityCollection
     */
    private $zombies;
    public function __construct(StateReader $stateReader, Entity $ash, EntityCollection $humans, EntityCollection $zombies)
    {
        $this->stateReader = $stateReader;
        $this->ash = $ash;
        $this->humans = $humans;
        $this->zombies = $zombies;
    }
    public function updateState() : void
    {
        $this->stateReader->updateState($this->ash, $this->humans, $this->zombies);
    }
    public function getAction() : string
    {
    }
    public function cleanup() : void
    {
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
        $x = abs($postiionA->getX() - $postiionB->getX());
        $y = abs($postiionA->gety() - $postiionB->gety());
        return (int) sqrt($x * $x + $y * $y);
    }
    /**
     * Calculate distance in turns between Ash and all zombies
     *
     * @param Ash $ash
     * @param EntityCollection $zombieEntityCollection
     * @return int[]
     */
    private function timeToDie(Ash $ash, EntityCollection $zombieEntityCollection) : array
    {
        $zombies = [];
        foreach ($zombieEntityCollection->list() as $zombie) {
            // Turns
            $distance = $this->getDistance($ash->getPosition(), $zombie->getPosition()) / 1000;
            $zombies[$zombie->getId()] = intval($distance) - self::ASH_RANGE / self::ASH_MOVEMENT;
        }
        return $zombies;
    }
    private function timeToLive()
    {
    }
}
}

namespace CodeInGame\CodeVsZombies {
use CodeInGame\CodeVsZombies\Entity\Ash;
use CodeInGame\CodeVsZombies\Entity\Entity;
use CodeInGame\CodeVsZombies\Entity\EntityCollection;
$game = new Game(new StateReader(), new Ash(), new EntityCollection(Entity::HUMAN), new EntityCollection(Entity::ZOMBIE));
// game loop
while (true) {
    $game->updateState();
    echo $game->getActions();
    $game->cleanup();
}
}

namespace {
while (true) {
    // $ash = getAsh();
    // $humans = getHumans();
    // $zombies = getZombies();
    $zombies = ttDie($ash, $zombies);
    $humans = ttLive($humans, $zombies);
    $humans = ttSave($ash, $humans);
    $target = getTarget($zombies, $humans);
    echo $target['x'], ' ', $target['y'], "\n";
}
// function getAsh() : array
// {
//     fscanf(STDIN, "%d %d", $x, $y);
//     return [
//         'x' => $x,
//         'y' => $y
//     ];
// }
// function getHumans() : array
// {
//     $humans = [];
//     fscanf(STDIN, "%d", $humanCount);
//     for ($i = 0; $i < $humanCount; $i++) {
//         fscanf(STDIN, "%d %d %d", $humanId, $humanX, $humanY);
//         $humans[$humanId] = [
//             'x' => $humanX,
//             'y' => $humanY
//         ];
//     }
//     return $humans;
// }
// function getZombies() : array
// {
//     $zombies = [];
//     fscanf(STDIN, "%d", $zombieCount);
//     for ($i = 0; $i < $zombieCount; $i++) {
//         fscanf(STDIN, "%d %d %d %d %d", $zombieId, $zombieX, $zombieY, $zombieXNext, $zombieYNext);
//         $zombies[$zombieId] = [
//             'x' => $zombieX,
//             'y' => $zombieY,
//             'xNext' => $zombieXNext,
//             'yNext' => $zombieYNext
//         ];
//     }
//     return $zombies;
// }
function ttDie(array $ash, array $zombies) : array
{
    foreach ($zombies as $zombieId => $zombie) {
        $zombies[$zombieId]['distance'] = intval(getDistance($ash, $zombie) / 2000);
    }
    return $zombies;
}
function ttLive(array $humans, array $zombies) : array
{
    foreach ($humans as $humanId => $human) {
        $humans[$humanId]['distance']['zombie'] = intval(getDistance($human, getNearest($human, $zombies)) / 400);
    }
    return $humans;
}
function ttSave(array $ash, array $humans) : array
{
    foreach ($humans as $humanId => $human) {
        $humans[$humanId]['distance']['ash'] = intval(getDistance($ash, $human) / 1000);
    }
    return $humans;
}
function getNearest(array $source, array $opponents)
{
    $minDistance = 100000;
    $target = null;
    foreach ($opponents as $opponent) {
        $distance = getDistance($source, $opponent);
        if ($distance < $minDistance) {
            $minDistance = $distance;
            $target = $opponent;
        }
    }
    return $target;
}
// function getDistance($entityA, $entityB) : int
// {
//     $x = abs($entityA['x'] - $entityB['x']);
//     $y = abs($entityA['y'] - $entityB['y']);
//     return (int) sqrt(($x * $x) + ($y * $y));
// }
function getTarget(array $zombies, array $humans) : array
{
    if (count($zombies) == 1) {
        return array_pop($zombies);
    }
    foreach ($humans as $humanId => $human) {
        $threat = $human['distance']['zombie'] - $human['distance']['ash'];
        if ($threat < 0 && count($humans) > 1) {
            unset($humans[$humanId]);
            continue;
        }
        $humans[$humanId]['threat'] = $threat;
    }
    if (min(array_column($humans, 'threat')) > 1) {
        usort($zombies, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
        return array_shift($zombies);
    }
    usort($humans, function ($a, $b) {
        return $a['threat'] <=> $b['threat'];
    });
    return array_shift($humans);
}
function debug($value = '')
{
    error_log(var_export($value, true));
}
}

namespace CodeInGame\CodeVsZombies {
use CodeInGame\CodeVsZombies\Entity\Ash;
use CodeInGame\CodeVsZombies\Entity\EntityCollection;
use CodeInGame\CodeVsZombies\Entity\Human;
use CodeInGame\CodeVsZombies\Entity\Position;
use CodeInGame\CodeVsZombies\Entity\Zombie;
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

