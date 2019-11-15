<?php
namespace CodeInGame\FantasticBits {
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

namespace CodeInGame\FantasticBits {
use CodeInGame\FantasticBits\Location\DistanceCalculator;
use CodeInGame\FantasticBits\Location\Position;
use CodeInGame\FantasticBits\Map\Team;
use CodeInGame\FantasticBits\Map\Component\Goal;
class Game
{
    private const SCORE_LEFT = 0;
    private const SCORE_RIGHT = 1;
    /**
     * @var DistanceCalculator
     */
    private $distanceCalculator;
    /**
     * @var Goal
     */
    private $myGoal;
    /**
     * @var Team
     */
    private $myTeam;
    /**
     * @var Goal
     */
    private $opponentGoal;
    /**
     * @var Team
     */
    private $oppTeam;
    /**
     * @var Snaffle[]
     */
    private $snaffles;
    /**
     * @var StateReader
     */
    private $stateReader;
    public function __construct(StateReader $stateReader, DistanceCalculator $distanceCalculator)
    {
        $this->distanceCalculator = $distanceCalculator;
        $this->stateReader = $stateReader;
    }
    public function init() : void
    {
        $playDirection = $this->stateReader->getPlayDirection();
        $leftGoal = new Goal(new Position(0, 3750));
        $rightGoal = new Goal(new Position(16000, 3750));
        if ($playDirection == self::SCORE_LEFT) {
            $this->myGoal = $leftGoal;
            $this->opponentGoal = $rightGoal;
        }
        if ($playDirection == self::SCORE_RIGHT) {
            $this->myGoal = $rightGoal;
            $this->opponentGoal = $leftGoal;
        }
    }
    public function updateState() : void
    {
        [$this->myTeam, $this->oppTeam, $this->snaffles] = $this->stateReader->getGameState();
    }
    public function getActions() : array
    {
        $actions = [];
        foreach ($this->myTeam->getWizards() as $wizard) {
            $command = $wizard->getState() ? 'THROW' : 'MOVE';
            switch ($command) {
                case 'THROW':
                    $target = $this->opponentGoal->getGoalCentre();
                    $speed = 400;
                    break;
                case 'MOVE':
                default:
                    $snaffle = $this->distanceCalculator->getNearestFreeEntity($wizard->getPosition(), $this->snaffles);
                    $snaffle->setState(true);
                    $target = $snaffle->getPosition();
                    $speed = 100;
                    break;
            }
            $actions[] = "{$command} {$target->getX()} {$target->getY()} {$speed}";
        }
        return $actions;
    }
}
}

namespace CodeInGame\FantasticBits\Location {
use CodeInGame\FantasticBits\Map\Entity\AbstractEntity;
use CodeInGame\FantasticBits\Map\Entity\EntityCollection;
class DistanceCalculator
{
    public function getDistance(Position $positionA, Position $positionB) : int
    {
        $x = abs($positionA->getX() - $positionB->getX());
        $y = abs($positionA->gety() - $positionB->gety());
        return (int) sqrt($x * $x + $y * $y);
    }
    public function getNearestEntity(Position $position, EntityCollection $collection) : ?AbstractEntity
    {
        $minDistance = null;
        $nearest = null;
        foreach ($collection as $entity) {
            $distance = $this->getDistance($position, $entity->getPosition());
            if ($distance < $minDistance || is_null($minDistance)) {
                $minDistance = $distance;
                $nearest = $entity;
            }
        }
        return $nearest;
    }
    public function getNearestFreeEntity(Position $position, EntityCollection $collection) : ?AbstractEntity
    {
        $freeEntities = new EntityCollection();
        foreach ($collection as $entity) {
            if ($entity->getState() === false) {
                $freeEntities->add($entity);
            }
        }
        return $this->getNearestEntity($position, $freeEntities);
    }
}
}

namespace CodeInGame\FantasticBits\Location {
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
    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }
    public function __toString()
    {
        return $this->x . ' ' . $this->y;
    }
    public function getX() : int
    {
        return $this->x;
    }
    public function getY() : int
    {
        return $this->y;
    }
}
}

namespace CodeInGame\FantasticBits\Map\Component {
use CodeInGame\FantasticBits\Location\Position;
class Goal
{
    private const GOAL_WIDTH = 4000;
    /**
     * @var Position
     */
    private $centre;
    /**
     * @var GoalPost
     */
    private $northPost;
    /**
     * @var GoalPost
     */
    private $southPost;
    public function __construct(Position $position)
    {
        $this->centre = $position;
        $this->northPost = $this->getNorthPost($position);
        $this->southPost = $this->getSouthPost($position);
    }
    public function getGoalTop() : int
    {
        return $this->northPost->getY() - $this->northPost->getRadius() / 2;
    }
    public function getGoalCentre() : Position
    {
        return $this->centre;
    }
    public function getGoalBottom() : int
    {
        return $this->southPost->getY() + $this->northPost->getRadius() / 2;
    }
    private function getNorthPost(Position $position) : GoalPost
    {
        $yShift = self::GOAL_WIDTH / 2;
        return new GoalPost(new Position($position->getX(), $position->getY() + $yShift));
    }
    private function getSouthPost(Position $position) : GoalPost
    {
        $yShift = self::GOAL_WIDTH / 2;
        return new GoalPost(new Position($position->getX(), $position->getY() - $yShift));
    }
}
}

namespace CodeInGame\FantasticBits\Map\Component {
use CodeInGame\FantasticBits\Location\Position;
use CodeInGame\FantasticBits\Map\Interfaces\Mappable;
class Goalpost implements Mappable
{
    private const RADIUS = 300;
    /**
     * @var Position
     */
    private $position;
    /**
     * @var Int
     */
    private $radius;
    public function __construct(Position $position)
    {
        $this->position = $position;
        $this->radius = self::RADIUS;
    }
    public function getPosition() : Position
    {
        return $this->position;
    }
    public function getRadius() : int
    {
        return $this->radius;
    }
}
}

namespace CodeInGame\FantasticBits\Map\Entity {
use CodeInGame\FantasticBits\Location\Position;
use CodeInGame\FantasticBits\Map\Interfaces\Identifiable;
use CodeInGame\FantasticBits\Map\Interfaces\Mappable;
use CodeInGame\FantasticBits\Map\Interfaces\Moveable;
abstract class AbstractEntity implements Identifiable, Mappable, Moveable
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var Position
     */
    protected $heading;
    /**
     * @var Position
     */
    protected $position;
    /**
     * @var int
     */
    protected $radius;
    /**
     * @var int
     */
    protected $state;
    public function __construct(int $id, int $radius, Position $position, Position $heading, int $state)
    {
        $this->id = $id;
        $this->heading = $heading;
        $this->position = $position;
        $this->radius = $radius;
        $this->state = $state;
    }
    public function getId() : int
    {
        return $this->id;
    }
    public function getHeading() : Position
    {
        return $this->heading;
    }
    public function getPosition() : Position
    {
        return $this->position;
    }
    public function getRadius() : int
    {
        return $this->radius;
    }
    public function getState() : bool
    {
        return (bool) $this->state;
    }
    public function setState(bool $state) : void
    {
        $this->state = $state;
    }
}
}

namespace CodeInGame\FantasticBits\Map\Entity {
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
    public function add(AbstractEntity $entity) : void
    {
        if ($this->entityType === null) {
            $this->entityType = get_class($entity);
        }
        if ($this->entityType !== get_class($entity)) {
            throw new InvalidArgumentException('A collection may only contain one type of entity');
        }
        $this->collection[$entity->getId()] = $entity;
    }
    public function get(int $id) : ?AbstractEntity
    {
        foreach ($this->collection as $entity) {
            if ($entity->getId() == $id) {
                return $entity;
            }
        }
        return null;
    }
    public function getIterator() : ArrayIterator
    {
        return new ArrayIterator($this->collection);
    }
    public function set(AbstractEntity ...$collection) : void
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
}

namespace CodeInGame\FantasticBits\Map\Entity {
use CodeInGame\FantasticBits\Location\Position;
class Snaffle extends AbstractEntity
{
    private const RADIUS = 150;
    public function __construct(int $id, Position $position, Position $heading, int $state)
    {
        parent::__construct($id, self::RADIUS, $position, $heading, $state);
    }
}
}

namespace CodeInGame\FantasticBits\Map\Entity {
use CodeInGame\FantasticBits\Location\Position;
class Wizard extends AbstractEntity
{
    private const RADIUS = 400;
    public function __construct(int $id, Position $position, Position $heading, int $state)
    {
        parent::__construct($id, self::RADIUS, $position, $heading, $state);
    }
    public function getTeam() : int
    {
        return $this->team;
    }
    public function setTeam(int $team) : void
    {
        $this->team = $team;
    }
}
}

namespace CodeInGame\FantasticBits\Map\Interfaces {
interface Identifiable
{
    public function getId() : int;
}
}

namespace CodeInGame\FantasticBits\Map\Interfaces {
use CodeInGame\FantasticBits\Location\Position;
interface Mappable
{
    public function getPosition() : Position;
    public function getRadius() : int;
}
}

namespace CodeInGame\FantasticBits\Map\Interfaces {
use CodeInGame\FantasticBits\Location\Position;
interface Moveable
{
    public function getHeading() : Position;
}
}

namespace CodeInGame\FantasticBits\Map\Interfaces {
interface hasState
{
    public function getState() : bool;
}
}

namespace CodeInGame\FantasticBits\Map {
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
    public function getId() : int
    {
        return $this->id;
    }
    public function getMagic() : int
    {
        return $this->magic;
    }
    public function getScore() : int
    {
        return $this->score;
    }
    public function getWizards() : EntityCollection
    {
        return $this->wizards;
    }
    public function setWizards(EntityCollection $wizards) : void
    {
        $this->wizards = $wizards;
    }
}
}

namespace CodeInGame\FantasticBits {
use CodeInGame\FantasticBits\Location\DistanceCalculator;
$game = new Game(new StateReader(), new DistanceCalculator());
$game->init();
// game loop
while (true) {
    $game->updateState();
    foreach ($game->getActions() as $action) {
        echo $action . PHP_EOL;
    }
}
}

namespace CodeInGame\FantasticBits {
use CodeInGame\FantasticBits\Location\Position;
use CodeInGame\FantasticBits\Map\Team;
use CodeInGame\FantasticBits\Map\Entity\AbstractEntity;
use CodeInGame\FantasticBits\Map\Entity\EntityCollection;
use CodeInGame\FantasticBits\Map\Entity\Snaffle;
use CodeInGame\FantasticBits\Map\Entity\Wizard;
use InvalidArgumentException;
class StateReader
{
    private const SNAFFLE = 'SNAFFLE';
    private const FRIENDLY_WIZARD = 'WIZARD';
    private const OPPONENT_WIZARD = 'OPPONENT_WIZARD';
    private const WIZARD = [self::FRIENDLY_WIZARD, self::OPPONENT_WIZARD];
    public function getPlayDirection() : int
    {
        fscanf(STDIN, '%d', $playDirection);
        return $playDirection;
    }
    /**
     * @throws InvalidArgumentException
     */
    public function getGameState() : array
    {
        [$myScore, $myMagic] = $this->getTeamStats();
        [$oppScore, $oppMagic] = $this->getTeamStats();
        [$snaffles, $myPlayers, $oppPlayers] = $this->getEntityList();
        $myTeam = new Team(0, $myMagic, $myScore);
        $myTeam->setWizards($myPlayers);
        $oppTeam = new Team(1, $oppScore, $oppMagic);
        $oppTeam->setWizards($oppPlayers);
        return [$myTeam, $oppTeam, $snaffles];
    }
    private function getTeamStats() : array
    {
        fscanf(STDIN, '%d %d', $score, $magic);
        return [$score, $magic];
    }
    /**
     * @throws InvalidArgumentException
     */
    private function getEntityList() : array
    {
        fscanf(STDIN, '%d', $entities);
        $myPlayers = new EntityCollection();
        $oppPlayers = new EntityCollection();
        $snaffles = new EntityCollection();
        $entityList = [];
        for ($i = 0; $i < $entities; $i++) {
            $entity = $this->loadEntity();
            if ($entity instanceof Snaffle) {
                $snaffles->add($entity);
                continue;
            }
            if ($entity instanceof Wizard && $entity->getTeam() == 0) {
                $myPlayers->add($entity);
                continue;
            }
            if ($entity instanceof Wizard && $entity->getTeam() == 1) {
                $oppPlayers->add($entity);
                continue;
            }
            throw new InvalidArgumentException('Invalid Entity Type');
        }
        return [$snaffles, $myPlayers, $oppPlayers];
    }
    /**
     * @throws InvalidArgumentException
     */
    private function loadEntity() : AbstractEntity
    {
        fscanf(STDIN, '%d %s %d %d %d %d %d', $entityId, $entityType, $x, $y, $vx, $vy, $state);
        if ($entityType == self::SNAFFLE) {
            return new Snaffle($entityId, new Position($x, $y), new Position($vx, $vy), $state);
        }
        if (in_array($entityType, self::WIZARD)) {
            $wizard = new Wizard($entityId, new Position($x, $y), new Position($vx, $vy), $state);
            $wizard->setTeam($entityType == self::OPPONENT_WIZARD);
            return $wizard;
        }
        throw new InvalidArgumentException('Unknown Entity Type: ' . $entityType);
    }
}
}

