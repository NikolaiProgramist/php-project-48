<?php

namespace Differ\Formatters\Stylish;

const ADD_MARKER = '+';
const REMOVE_MARKER = '-';
const UNCHANGED_MARKER = ' ';

function stylish($tree, string $replacer = ' ', int $spacesCount = 4): string
{
    $result = getStylish($tree, $replacer, $spacesCount);
    return "{\n{$result}}";
}

function getStylish($tree, string $replacer, int $spacesCount, int $depth = 1): string
{
    return array_reduce(
        array_keys($tree),
        function ($acc, $key) use ($tree, $replacer, $spacesCount, $depth) {
            $keyData = $tree[$key];
            $status = $tree[$key]['status'] ?? 'add';

            $status = match ($status) {
                'add' => ADD_MARKER,
                'remove' => REMOVE_MARKER,
                default => UNCHANGED_MARKER
            };

            $indentationCount = $spacesCount * $depth - 2;
            $indentation = str_repeat($replacer, $indentationCount);

            if (array_key_exists('value', $keyData)) {
                $value = $keyData['value'];

                if (!is_array($value)) {
                    $acc .= getStylishString($status, $key, $value, $indentation);
                    return $acc;
                }

                if ($status === ADD_MARKER) {
                    $innerContent = getArrayContent($value, $replacer, $spacesCount, $depth + 1);
                    $acc .= getStylishInnerContent($status, $key, $innerContent, $indentation);
                    return $acc;
                }

                $innerContent = getStylish($keyData['value'], $replacer, $spacesCount, $depth + 1);
                $acc .= getStylishInnerContent($status, $key, $innerContent, $indentation);
                return $acc;
            }

            $acc .= getChangedString(
                REMOVE_MARKER,
                $keyData['beforeValue'],
                $key,
                $indentation,
                $replacer,
                $spacesCount,
                $depth
            );

            $acc .= getChangedString(
                ADD_MARKER,
                $keyData['afterValue'],
                $key,
                $indentation,
                $replacer,
                $spacesCount,
                $depth
            );

            return $acc;
        },
        ''
    );
}

function getArrayContent($tree, string $replacer, int $spacesCount, int $depth): string
{
    return array_reduce(array_keys($tree), function ($acc, $key) use ($tree, $replacer, $spacesCount, $depth) {
        $value = $tree[$key]['value'];
        $indentationCount = $spacesCount * $depth - 2;
        $indentation = str_repeat($replacer, $indentationCount);

        if (!is_array($value)) {
            $acc .= getStylishString(UNCHANGED_MARKER, $key, $value, $indentation);
            return $acc;
        }

        $innerContent = getArrayContent($value, $replacer, $spacesCount, $depth + 1);
        return getStylishInnerContent(UNCHANGED_MARKER, $key, $innerContent, $indentation);
    }, '');
}

function getStylishInnerContent(string $marker, string $key, string $innerContent, string $indentation): string
{
    $result = $indentation . $marker . " {$key}: {\n{$innerContent}";
    $result .= $indentation . "  }\n";
    return $result;
}

function getStylishString(string $marker, string $key, $value, string $indentation): string
{
    $keyValue = getString($value);

    if ($value === '') {
        return $indentation . "{$marker} {$key}:\n";
    } else {
        return $indentation . "{$marker} {$key}: {$keyValue}\n";
    }
}

function getChangedString(
    string $marker,
    $value,
    string $key,
    string $indentation,
    string $replacer,
    string $spacesCount,
    int $depth
): string {
    if (!is_array($value)) {
        $result = getStylishString($marker, $key, $value, $indentation);
    } else {
        $innerContent = getStylish($value, $replacer, $spacesCount, $depth + 1);
        $result = getStylishInnerContent($marker, $key, $innerContent, $indentation);
    }

    return $result;
}

function getString($string): string
{
    if (is_bool($string)) {
        return $string ? 'true' : 'false';
    }

    if (is_null($string)) {
        return 'null';
    }

    return $string;
}
