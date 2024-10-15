<?php

namespace Gendiff\Parser;

use function cli\line;

function parse(string $pathFirst, string $pathSecond): void
{
    $filesContent = getFileContent($pathFirst, $pathSecond);

    line($filesContent['firstFileContent']);
    line($filesContent['secondFileContent']);
}

function getFileContent(string $pathFirst, string $pathSecond): array
{
    if (!str_starts_with($pathFirst, '/')) {
        $pathFirstFile = __DIR__ . '/../' . $pathFirst;
        $pathSecondFile = __DIR__ . '/../' . $pathSecond;
    } else {
        $pathFirstFile = $pathFirst;
        $pathSecondFile = $pathSecond;
    }

    if (!file_exists($pathFirstFile) && !file_exists($pathSecondFile)) {
        line('File(s) don\'t exists!');
        exit;
    }

    return [
        'firstFileContent' => file_get_contents($pathFirstFile),
        'secondFileContent' => file_get_contents($pathSecondFile),
    ];
}
