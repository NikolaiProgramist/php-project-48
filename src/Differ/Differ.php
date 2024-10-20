<?php

namespace Differ\Differ;

use function Functional\sort;

const ADD_MARKER = '+';
const REMOVE_MARKER = '-';
const UNCHANGED_MARKER = ' ';

function genDiff(array $filesContent): string
{
    $firstFile = json_decode($filesContent['firstFileContent'], true);
    $secondFile = json_decode($filesContent['secondFileContent'], true);

    $firstFileAdd = array_diff_assoc($secondFile, $firstFile);
    $addCollection = getStringAddElements($firstFileAdd);

    $firstFileRemove = array_diff_assoc($firstFile, $secondFile);
    $removeCollection = getStringRemoveElements($firstFileRemove, $firstFileAdd);

    $firstFileUnchanged = array_intersect($firstFile, $secondFile);
    $unchangedCollection = getStringUnchangedElements($firstFileUnchanged);

    $diffSorted = sort(
        array_merge_recursive($addCollection, $removeCollection, $unchangedCollection),
        fn($left, $right) => strcmp($left['keyName'], $right['keyName']),
        true
    );

    return translateDiffToString($diffSorted);
}

function getStringAddElements(array $diff): array
{
    return array_reduce(
        array_keys($diff),
        fn($collection, $key) => array_merge($collection, [
            $key => ['keyName' => $key, ADD_MARKER => $diff[$key]]
        ]),
        []
    );
}

function getStringRemoveElements(array $diff, array $addCollection): array
{
    return array_reduce(
        array_keys($diff),
        function ($collection, $key) use ($diff, $addCollection) {
            if (!array_key_exists($key, $addCollection)) {
                return array_merge($collection, [
                    $key => ['keyName' => $key, REMOVE_MARKER => $diff[$key]]
                ]);
            }

            return array_merge($collection, [
                $key => [REMOVE_MARKER => $diff[$key]]
            ]);
        },
        []
    );
}

function getStringUnchangedElements(array $diff): array
{
    return array_reduce(
        array_keys($diff),
        function ($collection, $element) use ($diff) {
            $collection[$element] = ['keyName' => $element, UNCHANGED_MARKER => $diff[$element]];
            return $collection;
        },
        []
    );
}

function translateDiffToString(array $diff): string
{
    $diffString = array_reduce($diff, function ($acc, $key) use ($diff) {
        $keyName = $key['keyName'];

        if (array_key_exists(REMOVE_MARKER, $key)) {
            $keyValue = $key[REMOVE_MARKER];
            $acc .= getStringWithMarker(REMOVE_MARKER, $keyName, $keyValue);
        }

        if (array_key_exists(ADD_MARKER, $key)) {
            $keyValue = $key[ADD_MARKER];
            $acc .= getStringWithMarker(ADD_MARKER, $keyName, $keyValue);
        }

        if (array_key_exists(UNCHANGED_MARKER, $key)) {
            $keyValue = $key[UNCHANGED_MARKER];
            $acc .= getStringWithMarker(UNCHANGED_MARKER, $keyName, $keyValue);
        }

        return $acc;
    }, '');

    return "{\n{$diffString}}\n";
}

function getStringWithMarker(string $marker, string $name, mixed $value): string
{
    return sprintf("  %s %s: %s\n", $marker, $name, boolToString($value));
}

function boolToString(mixed $string): string
{
    if (is_bool($string)) {
        return $string ? 'true' : 'false';
    }

    return $string;
}
