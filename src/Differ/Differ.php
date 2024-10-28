<?php

namespace Differ\Differ\Differ;

use function Differ\Formatters\selectFormatter;
use function Functional\sort;
use function Differ\Differ\Translator\getJson;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    $firstFile = getJson($pathToFile1);
    $secondFile = getJson($pathToFile2);

    $elements = sortingFirstFile($firstFile, $secondFile);
    $elements2 = sortingSecondFile($secondFile, $firstFile);

    $resultDiff = sortArrayByKeysRecursive(array_merge_recursive($elements, $elements2));
    return selectFormatter($resultDiff, $format);
}

function sortingFirstFile($tree1, $tree2)
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
            $acc[$key]['key'] = $key;

            if (!array_key_exists($key, $tree2)) {
                $acc[$key]['status'] = 'remove';
                $innerContent = sortingFirstFile($tree1[$key], $tree1[$key]);
                $acc[$key]['value'] = $innerContent;
                return $acc;
            }

            if ($tree1[$key] === $tree2[$key]) {
                $acc[$key]['status'] = 'unchanged';
                $acc[$key]['value'] = sortingFirstFile($tree1[$key], $tree1[$key]);
                return $acc;
            }

            if (is_array($tree1[$key]) && is_array($tree2[$key])) {
                $acc[$key]['status'] = 'changed';
                $acc[$key]['value'] = sortingFirstFile($tree1[$key], $tree2[$key]);
            } else {
                $acc[$key]['status'] = 'remove';
                $acc[$key]['beforeValue'] = sortingFirstFile($tree1[$key], $tree1[$key]);
                $acc[$key]['afterValue'] = sortingFirstFile($tree2[$key], $tree2[$key]);
            }

            return $acc;
        },
        []
    );
}

function sortingSecondFile($tree2, $tree1)
{
    if (!is_array($tree2)) {
        return $tree2;
    }

    return array_reduce(
        array_keys($tree2),
        function ($acc, $key) use ($tree1, $tree2) {
            if (!array_key_exists($key, $tree1)) {
                $acc[$key]['value'] = getArrayContent($tree2[$key]);
                return $acc;
            }

            if (is_array($tree2[$key]) && is_array($tree1[$key])) {
                $acc[$key]['value'] = sortingSecondFile($tree2[$key], $tree1[$key]);
                return $acc;
            }

            return $acc;
        },
        []
    );
}

function getArrayContent($tree)
{
    if (!is_array($tree)) {
        return $tree;
    }

    return array_reduce(array_keys($tree), function ($acc, $key) use ($tree) {
        $acc[$key]['value'] = getArrayContent($tree[$key]);
        return $acc;
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
        if (!is_array($tree[$key])) {
            $acc[$key] = $tree[$key];
            return $acc;
        }

        $innerContent = sortArrayByKeysRecursive($tree[$key]);
        $acc[$key] = $innerContent;
        return $acc;
    }, []);
}
