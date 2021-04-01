<?php

$files = scanFilepath(__DIR__ . '/src/');

return $files;

function scanFilepath($filepathBase): array
{
    $files = scandir($filepathBase);

    foreach ($files as $key => $filepath) {
        if ($filepath[0] === '.') {
            unset($files[$key]);
            continue;
        }

        $files[$key] = $filepathBase . $filepath;

        if (is_dir($filepathBase . $filepath)) {
            $files[$key] = scanFilepath($filepathBase . $filepath . '/');
        }
    }

    $completeList = [];
    foreach ($files as $key => $value) {
        if (is_array($value)) {
            $completeList = array_merge($completeList, $value);
            continue;
        }

        $completeList[] = $value;
    }

    return $completeList;
}
