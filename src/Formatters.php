<?php

namespace Differ\Formatters;

use function Differ\Formatters\Plain\plain;
use function Differ\Formatters\Stylish\stylish;

function selectFormatter(array $tree, string $format): string
{
    switch ($format) {
        case 'plain':
            return plain($tree);
        default:
            return stylish($tree);
    }
}
