<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Filesystem\Filesystem;
use JasonLewis\ResourceWatcher\Tracker;
use JasonLewis\ResourceWatcher\Watcher;

$base = $argv[1] ?? '';
$filepath = loadFilepath($base);

echo "Watching $filepath\n";

$watcher = new Watcher(new Tracker(), new Filesystem());
$listener = $watcher->watch($filepath);

$listener->anything(function() use ($base) {
    runCompiler("$base/mapper.php", "compiled/$base.php");
});

$watcher->start();

function loadFilepath(string $relativeFilepath): string
{
    return __DIR__ . '/' . $relativeFilepath . '/src/';
}

function runCompiler(string $mapperLocation, string $outputLocation): void
{
    shell_exec("sh vendor/bin/classpreloader.php compile --config {$mapperLocation} --output $outputLocation");
}
