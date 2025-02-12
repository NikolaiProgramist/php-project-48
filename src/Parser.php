<?php

namespace Differ\Parser;

use Exception;
use Symfony\Component\Yaml\Yaml;

use function Differ\Formatters\selectFormatter;

function parse(array $resultDiff, string $format): string
{
    return selectFormatter($resultDiff, $format);
}

/**
 * @throws Exception
 */
function parseToJson(string $path): array
{
    $content = getFileContent($path);
    $offset = strpos($path, '.');

    if ($offset === false) {
        throw new Exception("Incorrect path: {$path}");
    }

    $extension = substr($path, $offset + 1);
    $message = "The file format is not supported by the current version of the program: {$extension}";

    return match ($extension) {
        'json' => json_decode($content, true),
        'yaml' => Yaml::parse($content),
        default => throw new Exception($message)
    };
}

/**
 * @throws Exception
 */
function getFileContent(string $path): string
{
    $currentPath = realpath($path);

    if ($currentPath === false) {
        throw new Exception("File does not exist: {$path}");
    }

    return file_get_contents($currentPath);
}
