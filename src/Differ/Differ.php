<?php

namespace Differ\Differ\Differ;

use function Functional\sort;
use function Differ\Differ\Translator\getJson;
use function Differ\Formatters\Stylish\stylish;

const ADD_MARKER = '+';
const REMOVE_MARKER = '-';
const UNCHANGED_MARKER = ' ';

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    $firstFile = getJson($pathToFile1);
    $secondFile = getJson($pathToFile2);

    $sortingFirstFile = function ($acc, $key, $tree1, $tree2, $currentSorting)
    {
        if (!array_key_exists($key, $tree2)) {
            $acc[REMOVE_MARKER . $key] = $tree1[$key];
            return $acc;
        }

        $value1 = $tree1[$key];
        $value2 = $tree2[$key];

        if (!is_array($value1)) {
            if (is_array($value2)) {
                $acc[REMOVE_MARKER . $key] = $value1;
                $acc[ADD_MARKER . $key] = $value2;
                return $acc;
            }

            if ($value1 === $value2) {
                $acc[UNCHANGED_MARKER . $key] = $value1;
            } else {
                $acc[REMOVE_MARKER . $key] = $value1;
                $acc[ADD_MARKER . $key] = $value2;
            }

            return $acc;
        }

        if (!is_array($value2)) {
            $acc[REMOVE_MARKER . $key] = $value1;
            $acc[ADD_MARKER . $key] = $value2;
            return $acc;
        }

        $innerContent = getCustomDiff($value1, $value2, $currentSorting);
        $acc[$key] = $innerContent;
        return $acc;
    };

    $sortingSecondFile = function ($acc, $key, $tree2, $tree1, $currentSorting) {
        if (array_key_exists($key, $tree1)) {
            if (is_array($tree1[$key]) && is_array($tree2[$key])) {
                $innerContent = getCustomDiff($tree2[$key], $tree1[$key], $currentSorting);
                $acc[$key] = $innerContent;
                return $acc;
            }

            return $acc;
        }

        if (is_array($tree2[$key])) {
            $acc[ADD_MARKER . $key] = $tree2[$key];
            return $acc;
        }

        $acc[ADD_MARKER . $key] = $tree2[$key];
        return $acc;
    };

    $elements = getCustomDiff($firstFile, $secondFile, $sortingFirstFile);
    $elements2 = getCustomDiff($secondFile, $firstFile, $sortingSecondFile);

    $resultDiff = sortArrayByKeysRecursive(array_merge_recursive($elements, $elements2));

    switch ($format) {
        case 'stylish':
            return stylish($resultDiff);
    }
}

function getCustomDiff(array $tree1, array $tree2, callable $sorting): array
{
    $keysSorted = sort(
        array_keys($tree1),
        fn($left, $right) => strcmp($left, $right),
        true
    );

    return array_reduce(
        $keysSorted,
        fn($acc, $key) => $sorting($acc, $key, $tree1, $tree2, $sorting),
        []
    );
}

function sortArrayByKeysRecursive(array $tree): array
{
    $keysSorted = sort(
        array_keys($tree),
        function($left, $right) {
            $l = $left;
            $r = $right;

            $markers = [ADD_MARKER, REMOVE_MARKER, UNCHANGED_MARKER];

            if (in_array($left[0], $markers)) {
                $l = substr($left, 1);
            }

            if (in_array($right[0], $markers)) {
                $r = substr($right, 1);
            }

            return strcmp($l, $r);
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
