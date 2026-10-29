<?php

namespace CodeInGame\PowerOfThor;

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
     * @var Light
     */
    private $light;

    /**
     * @var Thor
     */
    private $thor;

    /**
     * @var int Turns Remaining
     */
    private $energy;

    /**
     * Create a new instance of this class
     */
    public function __construct()
    {
        fscanf(STDIN, "%d %d %d %d", $lightX, $lightY, $thorX, $thorY);

        $this->light = new Light($lightX, $lightY);
        $this->thor = new Thor($thorX, $thorY);
    }

    public function update()
    {
        fscanf(STDIN, "%d", $this->energy);
    }

    public function getLight(): Light
    {
        return $this->light;
    }

    public function getThor(): Thor
    {
        return $this->thor;
    }
}

class Light implements Plottable
{
    /**
     * @var int X co-ordinate
     */
    protected $x;

    /**
     * @var int Y co-ordinate
     */
    protected $y;

    /**
     * Create a new instance of this class
     *
     * @param int $x
     * @param int $y
     */
    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Fetch the X co-ordinate
     *
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * Fetch the Y co-ordinate
     *
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }
}

class Thor implements Plottable, Moveable
{
    /**
     * @var int X co-ordinate
     */
    protected $x;

    /**
     * @var int Y co-ordinate
     */
    protected $y;

    /**
     * Create a new instance of this class
     *
     * @param int $x
     * @param int $y
     */
    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Fetch the X co-ordinate
     *
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * Fetch the Y co-ordinate
     *
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }
    /**
     * Set the XY co-ordinates
     *
     * @param int $x
     * @param int $y
     */
    public function setXY(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }
}

interface Plottable
{
    /**
     * Fetch the X co-ordinate
     *
     * @return int
     */
    public function getX(): int;

    /**
     * Fetch the Y co-ordinate
     *
     * @return int
     */
    public function getY(): int;
}

interface Moveable
{
    /**
     * Set the XY co-ordinates
     *
     * @param int $x
     * @param int $y
     */
    public function setXY(int $x, int $y);
}

$gameState = new GameState();

while (true) {
    $gameState->update();

    $compareX = $gameState->getLight()->getX() <=> $gameState->getThor()->getX();
    $compareY = $gameState->getLight()->getY() <=> $gameState->getThor()->getY();

    $verticalDirection = [-1 => 'N', 0 => '', 1 => 'S'];
    $horizontalDirection = [-1 => 'W', 0 => '', 1 => 'E'];

    $gameState->getThor()->setXY(
        $gameState->getThor()->getX() + $compareX,
        $gameState->getThor()->getY() + $compareY
    );

    echo($verticalDirection[$compareY] . $horizontalDirection[$compareX] . "\n");
}
