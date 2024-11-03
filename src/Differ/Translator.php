<?php

namespace Differ\Translator;

use Symfony\Component\Yaml\Yaml;

use function Differ\Parser\parseError;

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

    // Colors for beautiful :)
    $normalColor = "\x1b[0m";
    $redColor = "\x1b[41m";

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

        parseError($errorMessage);
    }

    return file_get_contents($fullPath);
}
