<?php

$files = scandir(__DIR__ . '/src/');

foreach ($files as $key => $file) {
    if (in_array($file, ['.', '..'])) {
        unset($files[$key]);
        continue;
    }

    $files[$key] = __DIR__ . '/src/' . $file;
}

return $files;