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
            if (!array_key_exists($key, $tree2)) {
                $status = [$key => ['status' => 'remove']];
                $acc = array_merge($acc, $status);

                $innerContent = sortingFirstFile($tree1[$key], $tree1[$key]);
                $acc[$key]['value'] = $innerContent;
                return $acc;
            }

            if ($tree1[$key] === $tree2[$key]) {
                $status = [$key => ['status' => 'unchanged']];
                $acc = array_merge($acc, $status);

                $acc[$key]['value'] = sortingFirstFile($tree1[$key], $tree1[$key]);
                return $acc;
            }

            if (is_array($tree1[$key]) && is_array($tree2[$key])) {
                $status = [$key => ['status' => 'changed']];
                $acc = array_merge($acc, $status);

                $acc[$key]['value'] = sortingFirstFile($tree1[$key], $tree2[$key]);
            } else {
                $status = [$key => ['status' => 'remove']];
                $acc = array_merge($acc, $status);

                $acc[$key]['beforeValue'] = sortingFirstFile($tree1[$key], $tree1[$key]);
                $acc[$key]['afterValue'] = sortingFirstFile($tree2[$key], $tree2[$key]);
            }

            return $acc;
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

        if (!is_array($tree[$key])) {
            $diffValue[$key] = $tree[$key];
            return array_merge($diff, $diffValue);
        }

        $innerContent = sortArrayByKeysRecursive($tree[$key]);
        $diffContent[$key] = $innerContent;
        return array_merge($diff, $diffContent);
    }, []);
}
