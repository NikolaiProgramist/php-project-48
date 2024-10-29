<?php

namespace Differ\Differ;

use function Differ\Formatters\selectFormatter;
use function Functional\sort;
use function Differ\Translator\getJson;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    $firstFile = getJson($pathToFile1);
    $secondFile = getJson($pathToFile2);

    $elements = sortingFirstFile($firstFile, $secondFile);
    $elements2 = sortingSecondFile($secondFile, $firstFile);

    $resultDiff = sortArrayByKeysRecursive(array_merge_recursive($elements, $elements2));
    return selectFormatter($resultDiff, $format);
}

function sortingFirstFile(mixed $tree1, mixed $tree2): mixed
{
    if (!is_array($tree1)) {
        return $tree1;
    }

    if (!is_array($tree2)) {
        return $tree2;
    }

    return array_reduce(
        array_keys($tree1),
        function ($acc, $key) use ($tree1, $tree2) {
            $diff = [...$acc];

            if (!array_key_exists($key, $tree2)) {
                $diff[$key]['status'] = 'remove';
                $innerContent = sortingFirstFile($tree1[$key], $tree1[$key]);
                $diff[$key]['value'] = $innerContent;
                return $diff;
            }

            if ($tree1[$key] === $tree2[$key]) {
                $diff[$key]['status'] = 'unchanged';
                $diff[$key]['value'] = sortingFirstFile($tree1[$key], $tree1[$key]);
                return $diff;
            }

            if (is_array($tree1[$key]) && is_array($tree2[$key])) {
                $diff[$key]['status'] = 'changed';
                $diff[$key]['value'] = sortingFirstFile($tree1[$key], $tree2[$key]);
            } else {
                $diff[$key]['status'] = 'remove';
                $diff[$key]['beforeValue'] = sortingFirstFile($tree1[$key], $tree1[$key]);
                $diff[$key]['afterValue'] = sortingFirstFile($tree2[$key], $tree2[$key]);
            }

            return $diff;
        },
        []
    );
}

function sortingSecondFile(mixed $tree2, mixed $tree1): mixed
{
    if (!is_array($tree2)) {
        return $tree2;
    }

    return array_reduce(
        array_keys($tree2),
        function ($acc, $key) use ($tree1, $tree2) {
            $diff = [...$acc];
            $value = [];

            if (!array_key_exists($key, $tree1)) {
                $value[$key]['value'] = getArrayContent($tree2[$key]);
                return array_merge($diff, $value);
            }

            if (is_array($tree2[$key]) && is_array($tree1[$key])) {
                $value[$key]['value'] = sortingSecondFile($tree2[$key], $tree1[$key]);
                return array_merge($diff, $value);
            }

            return $diff;
        },
        []
    );
}

function getArrayContent(mixed $tree): mixed
{
    if (!is_array($tree)) {
        return $tree;
    }

    return array_reduce(array_keys($tree), function ($acc, $key) use ($tree) {
        $diff = [...$acc];
        $value = [];
        $value[$key]['value'] = getArrayContent($tree[$key]);
        return array_merge($diff, $value);
    }, []);
}

function sortArrayByKeysRecursive(array $tree): array
{
    $keysSorted = sort(
        array_keys($tree),
        function ($left, $right) {
            return strcmp($left, $right);
        },
        true
    );

    return array_reduce($keysSorted, function ($acc, $key) use ($tree) {
        $diff = [...$acc];
        $diffValue = [];
        $diffContent = [];

        if (!is_array($tree[$key])) {
            $diffValue[$key] = $tree[$key];
            return array_merge($diff, $diffValue);
        }

        $innerContent = sortArrayByKeysRecursive($tree[$key]);
        $diffContent[$key] = $innerContent;
        return array_merge($diff, $diffContent);
    }, []);
}
