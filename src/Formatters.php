<?php

namespace Differ\Formatters;

use function Differ\Formatters\Plain\plain;
use function Differ\Formatters\Stylish\stylish;
use function Differ\Formatters\Json\json;

function selectFormatter(array $tree, string $format): string
{
    switch ($format) {
        case 'plain':
            return plain($tree);
        case 'json':
            return json($tree);
        default:
            return stylish($tree);
    }
}
