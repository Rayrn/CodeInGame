<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Filesystem\Filesystem;
use JasonLewis\ResourceWatcher\Tracker;
use JasonLewis\ResourceWatcher\Watcher;

$base = $argv[1] ?? '';
$filepath = getAbsoluteFilepath($base);

echo "Watching $filepath\n";

$watcher = new Watcher(new Tracker(), new Filesystem());
$listener = $watcher->watch($filepath);

$listener->anything(function () use ($base) {
    runCompiler("$base/mapper.php", "compiled/$base.php");
});

$watcher->start();

function getAbsoluteFilepath(string $relativeFilepath): string
{
    return convertFilepathsToWindows(__DIR__ . '/' . $relativeFilepath . '/src/');
}

function runCompiler(string $mapperLocation, string $outputLocation): void
{
    $command = convertFilepathsToWindows("vendor/bin/classpreloader.php compile --config $mapperLocation --output $outputLocation");
    $output = shell_exec($command);

    # Debug
    echo convertFilepathsToUnix($command, '/'), PHP_EOL;
    echo $output;
}

/** Convert to windows style filepaths */
function convertFilepathsToWindows(string $filepath): string
{
    return str_replace('/', DIRECTORY_SEPARATOR, $filepath);
}

function convertFilepathsToUnix(string $filepath): string
{
    return str_replace(DIRECTORY_SEPARATOR, '/', $filepath);
}
