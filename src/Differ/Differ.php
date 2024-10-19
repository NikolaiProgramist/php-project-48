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
    $addCollection = array_reduce(
        array_keys($firstFileAdd),
        fn($collection, $key) => array_merge($collection, [
            $key => ['keyName' => $key, ADD_MARKER => $firstFileAdd[$key]]
        ]),
        []
    );

    $firstFileRemove = array_diff_assoc($firstFile, $secondFile);
    $removeCollection = array_reduce(
        array_keys($firstFileRemove),
        function ($collection, $key) use ($firstFileRemove, $addCollection) {
            if (!array_key_exists($key, $addCollection)) {
                return array_merge($collection, [
                    $key => ['keyName' => $key, REMOVE_MARKER => $firstFileRemove[$key]]
                ]);
            }

            return array_merge($collection, [
                $key => [REMOVE_MARKER => $firstFileRemove[$key]]
            ]);
        },
        []
    );

    $firstFileUnchanged = array_intersect($firstFile, $secondFile);
    $unchangedCollection = array_reduce(
        array_keys($firstFileUnchanged),
        function ($collection, $element) use ($firstFileUnchanged) {
            $collection[$element] = ['keyName' => $element, UNCHANGED_MARKER => $firstFileUnchanged[$element]];
            return $collection;
        },
        []
    );

    $diffSorted = sort(
        array_merge_recursive($addCollection, $removeCollection, $unchangedCollection),
        fn($left, $right) => strcmp($left['keyName'], $right['keyName']),
        true
    );

    return translateDiffToString($diffSorted);
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

    return "{\n{$diffString}}";
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
