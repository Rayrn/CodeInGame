<?php

namespace CodeInGame\Temperatures;

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
     * @var int Number of temperatures to analyze
     */
    private $count;

    /**
     * @var array List of temperatures
     */
    private $temperatures = [];

    /**
     * Create a new instance of this class
     */
    public function __construct()
    {
        fscanf(STDIN, "%d", $this->count);

        foreach (explode(" ", fgets(STDIN)) as $temperature) {
            $this->temperatures[] = intval($temperature);
        }
    }

    public function getTemperatures() : array
    {
        return $this->temperatures;
    }
}

$distance = 10000;
$closest = 10000;

foreach ((new GameState())->getTemperatures() as $temperature) {
    if ($temperature == 0) {
        $closest = 0;
        break;
    }

    if ($distance >= abs($temperature)) {
        $distance = abs($temperature);

        if (abs($temperature) == abs($closest)) {
            $temperature = $temperature > $closest ? $temperature : $closest;
        }

        $closest = $temperature;
    }
}

echo "$closest\n";