<?php

$gameState = new GameState();

while (true) {
    $gameState->update();

    $unassignedDrones = $gameState->getDroneAllocation();
    $zonePriority = [];

    foreach ($gameState->getZones() as $zone) {
        $zonePriority[$zone->getId()] = $zone->calculateRequiredDrones(
            $gameState->getNPCDrones(),
            $gameState->getPlayerID()
        );
    }

    asort($zonePriority);

    foreach ($zonePriority as $zoneId => $dronesRequired) {
        if ($dronesRequired > $unassignedDrones) {
            continue;
        }

        $zone = $gameState->getZone($zoneId);
        $distances = [];

        foreach ($gameState->getPlayerDrones() as $playerDrone) {
            $distances[$playerDrone->getId()] = $playerDrone->calculateDistanceFromZone($zone);
        }

        asort($distances);

        foreach (array_keys($distances) as $playerDroneId) {
            if ($gameState->getPlayerDrone($playerDroneId)->getAssignment()->getId() >= 0) {
                continue;
            }

            $gameState->getPlayerDrone($playerDroneId)->setAssignment($zone);
            $unassignedDrones -= 1;
            $dronesRequired -= 1;

            if ($dronesRequired < 1) {
                break;
            }
        }
    }

    foreach ($gameState->getPlayerDrones() as $playerDrone) {
        if ($playerDrone->getAssignment()->getId() < 0) {
            $distances = [];

            foreach ($gameState->getZones() as $zone) {
                $distances[$zone->getId()] = $playerDrone->calculateDistanceFromZone($zone);
            }

            asort($distances);
            reset($distances);

            $playerDrone->setAssignment($gameState->getZone(key($distances)));
        }

        echo "{$playerDrone->getAssignment()->getX()} {$playerDrone->getAssignment()->getY()}\n";
    }
}

/**
 * To debug (equivalent to var_dump)
 */
function debug($var)
{
    error_log(var_export($var, true));
}

class GameState
{
    /**
     * @var int Number of players in the game (2 to 4 players)
     */
    private $playerCount;

    /**
     * @var int ID of your player (0, 1, 2, or 3)
     */
    private $playerId;

    /**
     * @var int Number of drones in each team (3 to 11)
     */

    private $droneAllocation;

    /**
     * @var array Collection of Drone objects
     */
    private $drones;

    /**
     * @var int Number of zones on the map (4 to 8)
     */
    private $zoneCount;

    /**
     * @var array Collection of Zone objects
     */
    private $zones;

    public function __construct()
    {
        fscanf(STDIN, "%d %d %d %d", $this->playerCount, $this->playerId, $this->droneAllocation, $this->zoneCount);

        $this->zones = [];
        for ($z = 0; $z < $this->zoneCount; $z++) {
            fscanf(STDIN, "%d %d", $x, $y);

            $this->zones[$z] = new Zone($z, -1, $x, $y);
        }

        $this->drones = [];
        for ($p = 0; $p < $this->playerCount; $p++) {
            for ($d = 0; $d < $this->droneAllocation; $d++) {
                $this->drones[$p][$d] = new Drone($d, $p);
            }
        }
    }

    public function update()
    {
        for ($z = 0; $z < $this->zoneCount; $z++) {
            fscanf(STDIN, "%d", $teamId);

            $this->zones[$z]->setOwner($teamId);
        }

        for ($p = 0; $p < $this->playerCount; $p++) {
            for ($d = 0; $d < $this->droneAllocation; $d++) {
                fscanf(STDIN, "%d %d", $x, $y);
                $this->drones[$p][$d]->setX($x);
                $this->drones[$p][$d]->setY($y);
                $this->drones[$p][$d]->setAssignment(new Zone(-1, -1, -1, -1));
            }
        }
    }

    public function getPlayerCount(): int
    {
        return $this->playerCount;
    }

    public function getPlayerID(): int
    {
        return $this->playerId;
    }

    public function getDroneAllocation(): int
    {
        return $this->droneAllocation;
    }

    public function getZoneCount(): int
    {
        return $this->zoneCount;
    }

    public function getZones(): array
    {
        return $this->zones;
    }

    public function getZone(int $zoneId): Zone
    {
        return $this->zones[$zoneId];
    }

    public function getAllDrones(): array
    {
        return $this->drones;
    }

    public function getPlayerDrones(): array
    {
        return $this->drones[$this->playerId];
    }

    public function getPlayerDrone(int $droneId): Drone
    {
        return $this->drones[$this->playerId][$droneId];
    }

    public function getNPCDrones(): array
    {
        $drones = $this->drones;

        unset($drones[$this->playerId]);

        return $drones;
    }
}

class Zone
{
    /**
     * @var int Zone Id
     */
    private $id;

    /**
     * @var int ID of the team controlling the zone (0, 1, 2, or 3) or -1 if it is not controlled.
     */
    private $owner;

    /**
     * @var int X Coordinate
     */
    private $x;

    /**
     * @var int Y Coordinate
     */
    private $y;

    public function __construct(int $id, int $o, int $x, int $y)
    {
        $this->id = $id;
        $this->owner = $o;
        $this->x = $x;
        $this->y = $y;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOwner(): int
    {
        return $this->owner;
    }

    public function setOwner(int $owner)
    {
        $this->owner = $owner;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    /**
     * Calculate the number of drones required to hold/take the zone
     *
     * @param array $npcDroneSet
     */
    public function calculateRequiredDrones(array $npcDrones, int $playerId): int
    {
        $npcDronesInRange = [];

        foreach ($npcDrones as $npcDroneSet) {
            foreach ($npcDroneSet as $npcDrone) {
                $npcDronesInRange[$npcDrone->getOwner()] += $npcDrone->calculateDistanceFromZone($this) < 200;
            }
        }

        $dronesRequired = max($npcDronesInRange) + ($this->owner !== $playerId);

        return $dronesRequired ? $dronesRequired : 1;
    }
}

class Drone
{
    /**
     * @var int Zone Id
     */
    private $id;

    /**
     * @var int ID of the team controlling the drone (0, 1, 2, or 3)
     */
    private $owner;

    /**
     * @var int X Coordinate
     */
    private $x;

    /**
     * @var int Y Coordinate
     */
    private $y;

    /**
     * @var int Target assignment
     */
    private $assignment;

    public function __construct(int $id, int $owner)
    {
        $this->id = $id;
        $this->owner = $owner;
        $this->x = 0;
        $this->y = 0;
        $this->assignment = new Zone(-1, -1, -1, -1);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOwner(): int
    {
        return $this->owner;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function setX(int $x)
    {
        $this->x = $x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function setY(int $y)
    {
        $this->y = $y;
    }

    public function getAssignment(): Zone
    {
        return $this->assignment;
    }

    public function setAssignment(Zone $zone)
    {
        $this->assignment = $zone;
    }

    /**
     * Calculate the distance between a zone and the drone
     *
     * @param Zone $zone
     * @return int
     */
    public function calculateDistanceFromZone(Zone $zone): int
    {
        $distanceX = pow($this->x - $zone->getX(), 2);
        $distanceY = pow($this->y - $zone->getY(), 2);

        return intval(sqrt(abs($distanceX) + abs($distanceY)));
    }
}
