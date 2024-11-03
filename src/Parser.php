<?php

namespace Differ\Parser;

use function Differ\Differ\genDiff;

function parse(string $pathToFile1, string $pathToFile2, string $format): void
{
    $resultString = genDiff($pathToFile1, $pathToFile2, $format);
    echo $resultString;
}
