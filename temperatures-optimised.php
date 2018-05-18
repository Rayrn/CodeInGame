<?php

namespace CodeInGame\Temperatures\Optimised;

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

    public function getClosestTemp() : int
    {
        if (empty($this->temperatures) || isset(array_flip($this->temperatures)[0])) {
            return 0;
        }

        if (min($this->temperatures) > 0) {
            return min($this->temperatures);
        }

        if (max($this->temperatures) < 0) {
            return max($this->temperatures);
        }

        $temps = $this->temperatures;
        sort($temps);

        do {
            $key = round(count($temps) / 2);

            if ($temps[$key] > 0) {
                if (abs($temps[$key - 1]) >= abs($temps[$key])) {
                    return $temps[$key];
                }

                $temps = array_slice($temps, 0, $key);
            } else {
                if (abs($temps[$key + 1]) < abs($temps[$key])) {
                    return $temps[$key];
                }

                $temps = array_slice($temps, $key);
            }
        } while (count($temps) > 1);

        return $temps[0];
    }
}

echo (new GameState())->getClosestTemp() . "\n";