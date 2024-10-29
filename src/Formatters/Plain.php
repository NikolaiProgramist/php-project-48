<?php

namespace Differ\Formatters\Plain;

const ADD_MARKER = 'added';
const REMOVE_MARKER = 'removed';
const UPDATED_MARKER = 'updated';

function plain(array $tree, string $path = '', int $depth = 1): string
{
    return array_reduce(
        array_keys($tree),
        function ($acc, $key) use ($tree, $path, $depth) {
            $keyData = $tree[$key];
            $status = $tree[$key]['status'] ?? 'add';
            $newPath = $depth === 1 ? $key : "{$path}.{$key}";
            $addMarker = ADD_MARKER;
            $removeMarker = REMOVE_MARKER;
            $updatedMarker = UPDATED_MARKER;
            $resultString = $acc;

            if (array_key_exists('value', $keyData)) {
                $value = getString($keyData['value']);

                if ($status === 'add') {
                    $string = "Property '{$newPath}' was {$addMarker} with value: {$value}\n";
                    return "{$resultString}{$string}";
                }

                if ($status === 'remove') {
                    $string = "Property '{$newPath}' was {$removeMarker}\n";
                    return "{$resultString}{$string}";
                }

                if (is_array($keyData['value'])) {
                    $string = plain($keyData['value'], $newPath, $depth + 1);
                    return "{$resultString}{$string}";
                }

                return $resultString;
            }

            $beforeValue = getString($keyData['beforeValue']);
            $afterValue = getString($keyData['afterValue']);

            $string = "Property '{$newPath}' was {$updatedMarker}. From {$beforeValue} to {$afterValue}\n";
            return "{$resultString}{$string}";
        },
        ''
    );
}

function getString(mixed $string): string
{
    if (is_bool($string)) {
        return $string ? 'true' : 'false';
    }

    if (is_null($string)) {
        return 'null';
    }

    if (is_array($string)) {
        return '[complex value]';
    }

    if (is_string($string)) {
        return "'{$string}'";
    }

    return $string;
}
