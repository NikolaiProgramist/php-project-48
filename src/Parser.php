<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

use function Differ\Formatters\selectFormatter;

function parse(array $resultDiff, string $format): string
{
    return selectFormatter($resultDiff, $format);
}

function parseToJson(string $path): array
{
    $content = getFileContent($path);
    $offset = strpos($path, '.');

    if ($offset === false) {
        throw new \Exception("Incorrect path");
    }

    $extension = substr($path, $offset + 1);

    return match ($extension) {
        'json' => json_decode($content, true),
        'yaml' => Yaml::parse($content),
        default => throw new \Exception("There is no such extension: {$extension}")
    };
}

function getFileContent(string $path): string
{
    $dirPath = __DIR__;

    if (str_starts_with($path, '/')) {
        $fullPath = $path;
    } else {
        $fullPath = "{$dirPath}/../../{$path}";
    }

    if (!file_exists($fullPath)) {
        throw new \Exception("Incorrect path");
    }

    return file_get_contents($fullPath);
}
