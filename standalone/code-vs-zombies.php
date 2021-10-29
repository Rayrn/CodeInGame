<?php

while (true) {
    $ash = getAsh();
    $humans = getHumans();
    $zombies = getZombies();

    $zombies = ttDie($ash, $zombies);
    $humans = ttLive($humans, $zombies);
    $humans = ttSave($ash, $humans);

    $target = getTarget($zombies, $humans);

    echo $target['x'], ' ', $target['y'], "\n";
}

function getAsh(): array
{
    fscanf(STDIN, "%d %d", $x, $y);

    return [
        'x' => $x,
        'y' => $y
    ];
}

function getHumans(): array
{
    $humans = [];

    fscanf(STDIN, "%d", $humanCount);

    for ($i = 0; $i < $humanCount; $i++) {
        fscanf(STDIN, "%d %d %d", $humanId, $humanX, $humanY);

        $humans[$humanId] = [
            'x' => $humanX,
            'y' => $humanY
        ];
    }

    return $humans;
}

function getZombies(): array
{
    $zombies = [];

    fscanf(STDIN, "%d", $zombieCount);

    for ($i = 0; $i < $zombieCount; $i++) {
        fscanf(STDIN, "%d %d %d %d %d", $zombieId, $zombieX, $zombieY, $zombieXNext, $zombieYNext);

        $zombies[$zombieId] = [
            'x' => $zombieX,
            'y' => $zombieY,
            'xNext' => $zombieXNext,
            'yNext' => $zombieYNext
        ];
    }

    return $zombies;
}

function ttDie(array $ash, array $zombies): array
{
    foreach ($zombies as $zombieId => $zombie) {
        $zombies[$zombieId]['distance'] = intval(getDistance($ash, $zombie) / 2000);
    }

    return $zombies;
}

function ttLive(array $humans, array $zombies): array
{
    foreach ($humans as $humanId => $human) {
        $humans[$humanId]['distance']['zombie'] = intval(getDistance($human, getNearest($human, $zombies)) / 400);
    }

    return $humans;
}

function ttSave(array $ash, array $humans): array
{
    foreach ($humans as $humanId => $human) {
        $humans[$humanId]['distance']['ash'] = intval(getDistance($ash, $human) / 1000);
    }

    return $humans;
}

function getNearest(array $source, array $opponents)
{
    $minDistance = 100000;
    $target = null;

    foreach ($opponents as $opponent) {
        $distance = getDistance($source, $opponent);

        if ($distance < $minDistance) {
            $minDistance = $distance;
            $target = $opponent;
        }
    }

    return $target;
}

function getDistance($entityA, $entityB): int
{
    $x = abs($entityA['x'] - $entityB['x']);
    $y = abs($entityA['y'] - $entityB['y']);

    return (int) sqrt(($x * $x) + ($y * $y));
}

function getTarget(array $zombies, array $humans): array
{
    if (count($zombies) == 1) {
        return array_pop($zombies);
    }

    foreach ($humans as $humanId => $human) {
        $threat = $human['distance']['zombie'] - $human['distance']['ash'];

        if ($threat < 0 && count($humans) > 1) {
            unset($humans[$humanId]);
            continue;
        }

        $humans[$humanId]['threat'] = $threat;
    }

    if (min(array_column($humans, 'threat')) > 1) {
        usort($zombies, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        return array_shift($zombies);
    }

    usort($humans, function ($a, $b) {
        return $a['threat'] <=> $b['threat'];
    });

    return array_shift($humans);
}

function debug($value = '')
{
    error_log(var_export($value, true));
}
