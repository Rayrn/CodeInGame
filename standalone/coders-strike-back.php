<?php
// To debug (equivalent to var_dump): error_log(var_export($var, true));

// Persist
$checkpoints = [];
$lastX = 0;
$lastY = 0;
$lastThrust = 0;

while (TRUE)
{
    // Us
    fscanf(STDIN, "%d %d %d %d %d %d",
        $x,
        $y,
        $nextCheckpointX, // x position of the next check point
        $nextCheckpointY, // y position of the next check point
        $nextCheckpointDist, // distance to the next checkpoint
        $nextCheckpointAngle // angle between your pod orientation and the direction of the next checkpoint
    );

    // Them
    fscanf(STDIN, "%d %d", $opponentX, $opponentY);

    // Update
    $checkpoints = saveCheckpoint($nextCheckpointX, $nextCheckpointY, $checkpoints);

    // Change targets if we're close enough to rely on drift
    $target = getTarget($nextCheckpointX, $nextCheckpointY, $checkpoints, $nextCheckpointDist, $lastThrust);
    $thrust = getThrust($nextCheckpointAngle, $nextCheckpointDist);

    // Output command
    echo $target['x'] . ' ' . $target['y'] . ' ' . $thrust . PHP_EOL;
    
    $lastX = $x;
    $lastY = $y;
    $lastThrust = $thrust == 'BOOST' ? 100 : $thrust;
}

function getTarget($x, $y, $checkpoints, $distance, $lastThrust) {
    $resetDistance = (10 * $lastThrust) + 500;

    if ($distance < $resetDistance) {
        foreach ($checkpoints as $key => $data) {
            if ($data['x'] == $x && $data['y'] == $y) {
                if ($data == end($checkpoints) && $distance > ($resetDistance / 2)) {
                    return $checkpoints[$key];
                }
                return isset($checkpoints[$key + 1]) ? $checkpoints[$key + 1] : $checkpoints[0];
            }
        }
    }

    return ['x' => $x, 'y' => $y];
}

function getThrust($angle, $distance) {
    $angle = intval($angle);
    $angleMod = (100 - ($angle > 95 ? 95 : $angle)) / 100; 
 
    $thrust = intval((95 * $angleMod) + 5);

    if ($thrust >= 100) $thrust = $distance > 3000 ? 'BOOST' : 100;

    return $thrust;
}

function saveCheckpoint($x, $y, $checkpoints) {
    foreach ($checkpoints as $data) {
        if ($data['x'] == $x && $data['y'] == $y) {
            return $checkpoints;
        }
    }

    $checkpoints[] = ['x' => $x, 'y' => $y];

    return $checkpoints;
}
