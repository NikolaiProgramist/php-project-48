<?php

namespace Differ\Parser;

use JetBrains\PhpStorm\NoReturn;
use function cli\line;
use function Differ\Differ\genDiff;

#[NoReturn] function parse(string $string): void
{
    line('%s', $string);
    exit();
}

#[NoReturn] function parseDiff(string $pathToFile1, string $pathToFile2, string $format): void
{
    $resultString = genDiff($pathToFile1, $pathToFile2, $format);
    parse($resultString);
}

#[NoReturn] function parseError(string $errorMessage): void
{
    parse($errorMessage);
}
