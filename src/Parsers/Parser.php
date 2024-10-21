<?php

namespace Differ\Parsers\Parser;

use function cli\line;
use function Differ\Differ\Differ\genDiff;

function parse(string $pathToFile1, string $pathToFile2): void
{
    line(genDiff($pathToFile1, $pathToFile2));
}
