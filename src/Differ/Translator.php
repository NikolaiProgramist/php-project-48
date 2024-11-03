<?php

namespace Differ\Translator;

use Symfony\Component\Yaml\Yaml;

function getJson(string $path): array
{
    $content = getFileContent($path);

    if (str_ends_with($path, '.yaml') || str_ends_with($path, '.yml')) {
        return Yaml::parse($content);
    }

    return json_decode($content, true);
}

function getFileContent(string $path): string
{
    $dirPath = __DIR__;

    if (str_starts_with($path, '/')) {
        $fullPath = $path;
    } else {
        $fullPath = "{$dirPath}/../../{$path}";
    }

    return file_get_contents($fullPath);
}
