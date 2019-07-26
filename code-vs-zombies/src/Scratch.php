<?php

function getTarget(array $zombies, array $humans) : array
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
