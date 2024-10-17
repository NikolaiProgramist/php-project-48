<?php

namespace Differ\Differ;

use function Functional\flatten;
use function Functional\sort;

const ADD_MARKER = '+';
const REMOVE_MARKER = '-';
const UNCHANGED_MARKER = '=';

function genDiff(array $filesContent)
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
        fn($collection, $key) => array_merge($collection, [
            $key => ['keyName' => $key, REMOVE_MARKER => $firstFileRemove[$key]]
        ]),
        []
    );

    $firstFileUnchanged = array_intersect($firstFile, $secondFile);
    $unchangedCollection = array_reduce(array_keys($firstFileUnchanged), function ($collection, $element) use ($firstFileUnchanged) {
        $collection[$element] = ['keyName' => $element, UNCHANGED_MARKER => $firstFileUnchanged[$element]];
        return $collection;
    }, []);

    $diffSorted = sort(
        array_merge_recursive($addCollection, $removeCollection, $unchangedCollection),
        function ($left, $right) {
            $l = is_array($left['keyName']) ? $left['keyName'][0] : $left['keyName'];
            $r = is_array($right['keyName']) ? $right['keyName'][0] : $right['keyName'];

            return strcmp($l, $r);
        },
        true
    );

    return generateDiffToString($diffSorted);
}
