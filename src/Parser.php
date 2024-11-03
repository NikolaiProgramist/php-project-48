<?php

namespace Differ\Parser;

use function cli\line;
use function Differ\Differ\genDiff;

function parse(string $pathToFile1, string $pathToFile2, string $format): void
{
    $resultString = genDiff($pathToFile1, $pathToFile2, $format);
    line($resultString);
}
