<?php

$filepath = __DIR__ . '/../assets/cardlist.txt';
$handle = fopen($filepath, 'r');

$contents = fread($handle, filesize($filepath));

$cardData = '';
foreach (explode("\n", $contents) as $card) {
    [$number, $name, $type, $cost, $attack, $def, $abi, $php, $ehp, $cd, $desc] = explode(' ; ', $card);

    $data = [
        (int) $number, 
        trim($name), 
        trim($type), 
        (int) $cost, 
        (int) $attack, 
        (int) $def,
        trim($abi), 
        (int) $php, 
        (int) $ehp,
        (int) $cd,
        trim(preg_replace('/(\s|\\\\n)/', ' ', $desc))
    ];

    $cardData .= "    [{$data[0]}, '{$data[1]}', '{$data[2]}', {$data[3]}, {$data[4]}, {$data[5]}, '{$data[6]}', {$data[7]}, {$data[8]}, {$data[9]}, '{$data[10]}'], " . PHP_EOL;
}

echo '[' . PHP_EOL . $cardData . ']';