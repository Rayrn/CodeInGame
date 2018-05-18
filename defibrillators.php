<?php

namespace CodeInGame\Defibrillators;

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
     * @var User User's details
     */
    private $user;

    /**
     * @var float Number of defibrillators
     */
    private $count;

    /**
     * @var array List of defibrillators
     */
    private $defibrillators;

    /**
     * Create a new instance of this class
     */
    public function __construct()
    {
        fscanf(STDIN, "%s", $userLat);
        fscanf(STDIN, "%s", $userLong);

        $this->user = new User(
            $this->convertToFloat($userLat),
            $this->convertToFloat($userLong)
        );

        fscanf(STDIN, "%d", $this->count);

        for ($i = 0; $i < $this->count; $i++) { 
            $data = stream_get_line(STDIN, 256 + 1, "\n");
            $data = array_values(array_filter(explode(';', $data)));

            $this->defibrillators[] = new Defibrillator(
                $data[0],
                $data[1],
                $data[2],
                $this->convertToFloat($data[3]),
                $this->convertToFloat($data[4])
            );
        }
    }

    public function closest() : Defibrillator
    {
        $distances = [];

        debug($this);
    }

    private function convertToFloat(string $number) : float
    {
        return floatval(str_replace(',', '.', $number));
    }
}

interface Plottable
{
    /**
     * Fetch the X co-ordinate
     *
     * @return float
     */
    public function getX() : float;

    /**
     * Fetch the Y co-ordinate
     *
     * @return float
     */
    public function getY() : float;
}

class User implements Plottable
{
    /**
     * @var float X co-ordinate
     */
    protected $x;

    /**
     * @var float Y co-ordinate
     */
    protected $y;

    /**
     * Create a new instance of this class
     *
     * @param float $x
     * @param float $y
     */
    public function __construct(float $x, float $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Fetch the X co-ordinate
     *
     * @return float
     */
    public function getX() : float
    {
        return $this->x;
    }

    /**
     * Fetch the Y co-ordinate
     *
     * @return float
     */
    public function getY() : float
    {
        return $this->y;
    }
}

class Defibrillator implements Plottable
{
    /**
     * @var string Name
     */
    protected $id;

    /**
     * @var string Name
     */
    protected $name;

    /**
     * @var string Address
     */
    protected $address;

    /**
     * @var float X co-ordinate
     */
    protected $x;

    /**
     * @var float Y co-ordinate
     */
    protected $y;

    /**
     * Create a new instance of this class
     *
     * @param int $id
     * @param string $name
     * @param string $address
     * @param float $x
     * @param float $y
     */
    public function __construct(int $id, string $name, string $address, float $x, float $y)
    {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Fetch the Name
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Fetch the Name
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Fetch the Address
     *
     * @return string
     */
    public function getAddress() : string
    {
        return $this->address;
    }

    /**
     * Fetch the X co-ordinate
     *
     * @return float
     */
    public function getX() : float
    {
        return $this->x;
    }

    /**
     * Fetch the Y co-ordinate
     *
     * @return float
     */
    public function getY() : float
    {
        return $this->y;
    }
}

echo (new GameState())->closest() . "\n";