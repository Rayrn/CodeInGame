<?php

namespace CodeInGame\SpringChallenge2021;

class StateReader
{
    /** @var Game */
    private $game;

    public function __construct(Game $game)
    {
        $this->game = $game;

        $this->setUpGame();
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function updateState(): void
    {
        fscanf(STDIN, "%d", $cardCount);

        $cardData = [];
        for ($i = 0; $i < $cardCount; $i++) {
            fscanf(STDIN, "%d %d %d %d %d %d %d %s %d %d %d", $number, $instanceId, $location, $type, $cost, $att, $def, $abi, $myhealth, $opphealth, $draw);


            if ($instanceId == '-1') {
                $instanceId = $i - 3;
            }

            $cardData[] = [$this->cardFactory->create($number, $instanceId), $location];
        }
    }

    private function setUpGame(): void
    {
        fscanf(STDIN, "%d", $numberOfHexes);

        $hexMap = [];
        $hexes = [];
        for ($i = 0; $i < $numberOfHexes; $i++) {
            // $index: 0 is the center cell, the next cells spiral outwards
            // $richness: 0 if the cell is unusable, 1-3 for usable cells
            // $neigh0: the index of the neighbouring cell for each direction
            fscanf(STDIN, "%d %d %d %d %d %d %d %d", $index, $richness, $neigh0, $neigh1, $neigh2, $neigh3, $neigh4, $neigh5);

            $hexes[$index] = new Hex($index, $richness);
            $hexMap[$index] = [
                $neigh0 => $neigh0,
                $neigh1 => $neigh1,
                $neigh2 => $neigh2,
                $neigh3 => $neigh3,
                $neigh4 => $neigh4,
                $neigh5 => $neigh5
            ];
        }

        foreach ($hexes as $hex) {
            $hex->setNeighbours(...array_interset_key($hexMap[$hex->id], $hexes));
        }

        new Debug($hexes);
        exit();

        $this->game->setHexes($hexes);
    }
}

// // $numberOfCells: 37
// fscanf(STDIN, "%d", $numberOfCells);
// for ($i = 0; $i < $numberOfCells; $i++)
// {
//     // $index: 0 is the center cell, the next cells spiral outwards
//     // $richness: 0 if the cell is unusable, 1-3 for usable cells
//     // $neigh0: the index of the neighbouring cell for each direction
//     fscanf(STDIN, "%d %d %d %d %d %d %d %d", $index, $richness, $neigh0, $neigh1, $neigh2, $neigh3, $neigh4, $neigh5);
// }

// // game loop
// while (TRUE)
// {
//     // $day: the game lasts 24 days: 0-23
//     fscanf(STDIN, "%d", $day);

//     // $nutrients: the base score you gain from the next COMPLETE action
//     fscanf(STDIN, "%d", $nutrients);

//     // $sun: your sun points
//     // $score: your current score
//     fscanf(STDIN, "%d %d", $sun, $score);

//     // $oppSun: opponent's sun points
//     // $oppScore: opponent's score
//     // $oppIsWaiting: whether your opponent is asleep until the next day
//     fscanf(STDIN, "%d %d %d", $oppSun, $oppScore, $oppIsWaiting);

//     // $numberOfTrees: the current amount of trees
//     fscanf(STDIN, "%d", $numberOfTrees);
//     for ($i = 0; $i < $numberOfTrees; $i++)
//     {
//         // $cellIndex: location of this tree
//         // $size: size of this tree: 0-3
//         // $isMine: 1 if this is your tree
//         // $isDormant: 1 if this tree is dormant
//         fscanf(STDIN, "%d %d %d %d", $cellIndex, $size, $isMine, $isDormant);
//     }

//     // $numberOfPossibleActions: all legal actions
//     fscanf(STDIN, "%d", $numberOfPossibleActions);
//     for ($i = 0; $i < $numberOfPossibleActions; $i++)
//     {
//         $possibleAction = stream_get_line(STDIN, 31 + 1, "\n");// try printing something from here to start with
//     }

//     // Write an action using echo(). DON'T FORGET THE TRAILING \n
//     // To debug: error_log(var_export($var, true)); (equivalent to var_dump)


//     // GROW cellIdx | SEED sourceIdx targetIdx | COMPLETE cellIdx | WAIT <message>
//     echo("WAIT\n");
// }
