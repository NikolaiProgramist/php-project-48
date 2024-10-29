<?php

namespace Differ\Translator;

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
    $dirPath = __DIR__;

    if (str_starts_with($path, '/')) {
        $fullPath = $path;
    } else {
        $fullPath = "{$dirPath}/../../{$path}";
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
