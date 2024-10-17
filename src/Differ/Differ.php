<?php

namespace Differ\Differ;

use function Functional\sort;

const ADD_MARKER = '+';
const REMOVE_MARKER = '-';
const UNCHANGED_MARKER = '=';

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

    return translateDiffToString($diffSorted);
}

function translateDiffToString(array $diff): string
{
    $diffString = array_reduce($diff, function ($acc, $key) use ($diff) {
        $keyName = is_array($key['keyName']) ? $key['keyName'][0] : $key['keyName'];

        if (array_key_exists(REMOVE_MARKER, $key)) {
            $acc .= PHP_EOL . <<<DOC
              - {$keyName}: {$key[REMOVE_MARKER]}
            DOC;
        }

        if (array_key_exists(ADD_MARKER, $key)) {
            $acc .= PHP_EOL . <<<DOC
              + {$keyName}: {$key[ADD_MARKER]}
            DOC;
        }

        if (array_key_exists(UNCHANGED_MARKER, $key)) {
            $acc .= PHP_EOL . <<<DOC
                {$keyName}: {$key[UNCHANGED_MARKER]}
            DOC;
        }

        return $acc;
    }, '');

    return "{{$diffString}\n}";
}
