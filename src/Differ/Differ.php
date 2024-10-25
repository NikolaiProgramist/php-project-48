<?php

namespace Differ\Differ\Differ;

use function Functional\sort;
use function Differ\Differ\Translator\getJson;

const ADD_MARKER = '+';
const REMOVE_MARKER = '-';
const UNCHANGED_MARKER = ' ';

function genDiff(string $pathToFile1, string $pathToFile2): void
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

            $value1 = toString($tree1[$key]);
            $value2 = toString($tree2[$key]);

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

        $innerContent = stringifyTree($value1, $value2, $currentSorting);
        $acc[$key] = $innerContent;
        return $acc;
    };

    $sortingSecondFile = function ($acc, $key, $tree2, $tree1, $currentSorting) {
        if (array_key_exists($key, $tree1)) {
            if (is_array($tree1[$key]) && is_array($tree2[$key])) {
                $innerContent = stringifyTree($tree2[$key], $tree1[$key], $currentSorting);
                $acc[$key] = $innerContent;
                return $acc;
            }

            return $acc;
        }

        if (is_array($tree2[$key])) {
            $acc[ADD_MARKER . $key] = $tree2[$key];
            return $acc;
        }

        $acc[ADD_MARKER . $key] = toString($tree2[$key]);
        return $acc;
    };

    $elements = stringifyTree($firstFile, $secondFile, $sortingFirstFile);
    $elements2 = stringifyTree($secondFile, $firstFile, $sortingSecondFile);

    print_r(sortByKeysRecursive(array_merge_recursive($elements, $elements2)));
}

function stringifyTree(array $tree1, array $tree2, callable $sorting): array
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

function sortByKeysRecursive(array $tree): array
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

        $innerContent = sortByKeysRecursive($tree[$key]);
        $acc[$key] = $innerContent;
        return $acc;
    }, []);
}

function toString($string): string
{
    if (is_bool($string)) {
        return $string ? 'true' : 'false';
    }

    if (is_null($string)) {
        return 'null';
    }

    return $string;
}
