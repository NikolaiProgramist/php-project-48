<?php

namespace Gendiff\Parser;

$autoloadPathLocal = __DIR__ . '/../vendor/autoload.php';
$autoloadPathGlobal = __DIR__ . '/../../../autoload.php';

if (file_exists($autoloadPathLocal)) {
    require_once $autoloadPathLocal;
} else {
    require_once $autoloadPathGlobal;
}

use function cli\line;

function parse(string $pathFirstFile, string $pathSecondFile): void
{
    if (!file_exists($pathFirstFile) && !file_exists($pathSecondFile)) {
        line('File(s) don\'t exists!');
        exit;
    }

    line(file_get_contents($pathFirstFile));
    line(file_get_contents($pathSecondFile));
}
