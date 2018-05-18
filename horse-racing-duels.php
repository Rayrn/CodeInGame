<?php

namespace CodeInGame\HorseRacingDuels;

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
     * @var int Number of horses to analyze
     */
    private $count;

    /**
     * @var array List of horses
     */
    private $horses;

    /**
     * Create a new instance of this class
     */
    public function __construct()
    {
        fscanf(STDIN, "%d", $this->count);

        for ($i = 0; $i < $this->count; $i++) { 
            fscanf(STDIN, "%d", $this->horses[]);
        }
    }

    public function getClosestHorses() : int
    {
        if (count($this->horses) == 1) {
            return $this->horses[0];
        }

        if (count($this->horses) !== count(array_flip($this->horses))) {
            return 0;
        }

        sort($this->horses);

        $minDiff = 10000000;

        foreach ($this->horses as $key => $power) {
            $diff = $power - ($this->horses[$key - 1] ?? 0);

            if ($diff == 1) {
                return 1;
            }

            if ($diff < $minDiff) {
                $minDiff = $diff;
            }
        }

        return $minDiff;
    }
}

echo (new GameState())->getClosestHorses() . "\n";