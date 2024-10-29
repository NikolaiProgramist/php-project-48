<?php

namespace Differ\Formatters\Json;

function json(array $tree): string
{
    return json_encode($tree);
}
