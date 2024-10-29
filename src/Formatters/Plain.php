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

            if (array_key_exists('value', $keyData)) {
                $value = getString($keyData['value']);

                if ($status === 'add') {
                    $acc .= "Property '{$newPath}' was " . ADD_MARKER . " with value: {$value}\n";
                    return $acc;
                }

                if ($status === 'remove') {
                    $acc .= "Property '{$newPath}' was " . REMOVE_MARKER . "\n";
                    return $acc;
                }

                if (is_array($keyData['value'])) {
                    $acc .= plain($keyData['value'], $newPath, $depth + 1);
                }

                return $acc;
            }

            $beforeValue = getString($keyData['beforeValue']);
            $afterValue = getString($keyData['afterValue']);

            $acc .= "Property '{$newPath}' was " . UPDATED_MARKER . ". From {$beforeValue} to {$afterValue}\n";
            return $acc;
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
