<?php

$gameState = new GameState();

// game loop
while (true) {
    $gameState->update();

    for ($i = 0; $i < $gameState->getDroneAllocation(); $i++) {
        // Write an action using echo(). DON'T FORGET THE TRAILING \n
        // output a destination point to be reached by one of your drones. The first line corresponds to the first of your drones that you were provided as input, the next to the second, etc.
        echo("20 20\n");
    }
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

    /**
     * @var array Collection of Zone objects
     */
    private $debug;

    public function __construct()
    {
        fscanf(STDIN, "%d %d %d %d", $this->playerCount, $this->playerId, $this->droneAllocation, $this->zoneCount);

        $this->zones = [];
        for ($z = 0; $z < $this->zoneCount; $z++) {
            $this->zones[$z] = new Zone($z);
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
                fscanf(STDIN,"%d %d", $x, $y);
                $this->drones[$p][$d]->setX($x);
                $this->drones[$p][$d]->setY($y);
            }
        }
    }

    public function getPlayerCount() : int
    {
        return $this->playerCount;
    }

    public function getPlayerID() : int
    {
        return $this->playerId;
    }

    public function getDroneAllocation() : int
    {
        return $this->droneAllocation;
    }

    public function getZoneCount() : int
    {
        return $this->zoneCount;
    }

    public function getZones() : int
    {
        return $this->zoneCount;
    }

    /**
     * To debug (equivalent to var_dump): `error_log(var_export($var, true));`
     */
    public function debug()
    {
        error_log(var_export($this, true));
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

    public function __construct($id)
    {
        $this->id = $id;
        $this->owner = -1;

        fscanf(STDIN, "%d %d", $this->x, $this->y);
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getOwner() : int
    {
        $this->owner = $owner;
    }

    public function setOwner(int $owner)
    {
        $this->owner = $owner;
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

    public function __construct($id, $owner)
    {
        $this->id = $id;
        $this->owner = $owner;
        $this->x = 0;
        $this->y = 0;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getOwner() : int
    {
        $this->owner = $owner;
    }

    public function getX() : int
    {
        return $this->x;
    }

    public function setX(int $x)
    {
        $this->x = $x;
    }

    public function getY() : int
    {
        return $this->y;
    }

    public function setY(int $y)
    {
        $this->y = $y;
    }
}
