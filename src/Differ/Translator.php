<?php

namespace Differ\Differ\Translator;

use Symfony\Component\Yaml\Yaml;

use function cli\line;

function getJson(string $path): array
{
    if (str_ends_with($path, '.yaml') || str_ends_with($path, '.yml')) {
        return Yaml::parseFile($path);
    }

    return json_decode(getFileContent($path), true);
}

function getFileContent(string $path): string
{
    // Colors for beautiful :)
    $normalColor = "\x1b[0m";
    $redColor = "\x1b[41m";

    if (str_starts_with($path, '/')) {
        $fullPath = $path;
    } else {
        $fullPath = __DIR__ . '/../../' . $path;
    }

    if (!file_exists($fullPath)) {
        $errorMessage = <<<DOC
        {$redColor}ERROR!{$normalColor}
        
        File: "{$fullPath}" don't exists!
        DOC;

        line($errorMessage);
        exit;
    }

    return file_get_contents($fullPath);
}
