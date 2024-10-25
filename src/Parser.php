<?php

namespace Differ\Parser;

use function cli\line;
use function Differ\Differ\Differ\genDiff;

function parse(string $pathToFile1, string $pathToFile2): void
{
    genDiff($pathToFile1, $pathToFile2);
}

//$indentationCount = $spacesCount * $depth;
//$indentation = str_repeat($replacer, $indentationCount);
