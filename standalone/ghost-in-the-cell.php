<?php

$map = new Map();

// game loop
while (true) {
    $map->updateState();

    echo implode(';', $map->setOrders()), "\n";
}

function debug(...$args)
{
    foreach ($args as $value) {
        error_log(print_r($value, true));
    }
}

class Factory
{
    public $routes = [];
    public $incoming = [];

    public $owner;
    public $cyborgs;
    public $production;
    public $sleep;
    public $arg5;

    public function __construct($routes)
    {
        $this->routes = $routes;
    }

    public function update($owner, $cyborgs, $production, $sleep, $arg5)
    {
        $this->owner = $owner;
        $this->cyborgs = $cyborgs;
        $this->production = $production;
        $this->sleep = $sleep;
        $this->arg5 = $arg5;
    }

    public function incoming()
    {
        $count = 0;

        foreach ($this->incoming as $cyborg) {
            $cyborg->owner ? $count -= $cyborg->size : $count += $cyborg->size;
        }

        return $count ? $count : 0;
    }
}

class Cyborg
{
    public $owner;
    public $size;
    public $distance;

    public function __construct($owner, $size, $distance)
    {
        $this->owner = $owner;
        $this->size = $size;
        $this->distance = $distance;
    }
}

class Map
{
    public $factories;

    public function __construct()
    {
        fscanf(STDIN, "%d", $factoryCount);
        fscanf(STDIN, "%d", $linkCount);

        $factories = [];

        for ($i = 0; $i < $linkCount; $i++) {
            fscanf(STDIN, "%d %d %d", $factory1, $factory2, $distance);

            $factories[$factory1][$factory2] = $distance;
            $factories[$factory2][$factory1] = $distance;
        }

        foreach ($factories as $factoryId => $factory) {
            asort($factories[$factoryId]);

            $this->factories[$factoryId] = new Factory($factories[$factoryId]);
        }
    }

    public function setOrders()
    {
        $orders = [];

            debug($this->getFactories());
        foreach ($this->getFactories() as $factoryId => $factory) {
            debug($this->factories);
            debug($this->factories[$factoryId]);

            if ($factory->production < 3 && ($factory->cyborgs - $factory->incoming() > 20)) {
                $orders[] = "INC $factoryId";
                continue;
            }

            if ($factory->cyborgs < 1) {
                continue;
            }

            foreach ($factory->routes as $linkId => $link) {
                if ($factory->cyborgs < 1 || $this->factories[$linkId]->owner != 1) {
                    continue;
                }

                $incoming = $this->factories[$linkId]->incoming() + 1;
                $orders[] = "MOVE $factoryId $linkId $incoming";

                $this->factories[$linkId]->cyborgs -= $incoming;
            }

            foreach ($factory->routes as $linkId => $link) {
                if ($factory->cyborgs < 1 || $this->factories[$linkId]->owner != 0) {
                    continue;
                }

                $incoming = $this->factories[$linkId]->incoming() + 1;
                $orders[] = "MOVE $factoryId $linkId $incoming";

                $this->factories[$linkId]->cyborgs -= $incoming;
            }

            foreach ($factory->routes as $linkId => $link) {
                if ($factory->cyborgs < 5 || $this->factories[$linkId]->owner != -1) {
                    continue;
                }

                $orders[] = "MOVE $factoryId $linkId {$this->factories[$linkId]->cyborgs}";

                $this->factories[$linkId]->cyborgs = 0;
                break;
            }
        }

        return $orders ?: ["WAIT"];
    }

    public function updateState()
    {
        foreach ($this->factories as $factory) {
            $factory->incoming = [];
        }

        fscanf(STDIN, "%d", $entityCount);

        for ($i = 0; $i < $entityCount; $i++) {
            fscanf(STDIN, "%d %s %d %d %d %d %d", $entityId, $entityType, $arg1, $arg2, $arg3, $arg4, $arg5);

            if ($entityType == 'FACTORY') {
                $this->factories[$entityId]->update($arg1, $arg2, $arg3, $arg4, $arg5);
            }

            if ($entityType == 'TROOP') {
                $this->factories[$arg3]->incoming[] = new Cyborg($arg1, $arg4, $arg5);
            }
        }
    }

    private function getFactories($owner = 1)
    {
        $factories = [];

        foreach ($this->factories as $factory) {
            if ($factory->owner == $owner) {
                $factories[] = $factory;
            }
        }

        return $factories;
    }
}