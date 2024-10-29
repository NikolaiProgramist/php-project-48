<?php

namespace Differ\Formatters\Stylish;

const ADD_MARKER = '+';
const REMOVE_MARKER = '-';
const UNCHANGED_MARKER = ' ';

function stylish(array $tree, string $replacer = ' ', int $spacesCount = 4): string
{
    $result = getStylish($tree, $replacer, $spacesCount);
    return "{\n{$result}}";
}

function getStylish(array $tree, string $replacer, int $spacesCount, int $depth = 1): string
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

            $data = [
                'indentation' => $indentation,
                'replacer' => $replacer,
                'spacesCount' => $spacesCount,
                'depth' => $depth];

            $acc .= getChangedString(
                REMOVE_MARKER,
                $keyData['beforeValue'],
                $key,
                $data
            );

            $acc .= getChangedString(
                ADD_MARKER,
                $keyData['afterValue'],
                $key,
                $data
            );

            return $acc;
        },
        ''
    );
}

function getArrayContent(array $tree, string $replacer, int $spacesCount, int $depth): string
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

function getStylishInnerContent(string $marker, int|string $key, string $innerContent, string $indentation): string
{
    $result = $indentation . $marker . " {$key}: {\n{$innerContent}";
    $result .= $indentation . "  }\n";
    return $result;
}

function getStylishString(string $marker, int|string $key, mixed $value, string $indentation): string
{
    $keyValue = getString($value);
    return $indentation . "{$marker} {$key}: {$keyValue}\n";
}

function getChangedString(string $marker, mixed $value, int|string $key, array $data): string
{
    if (!is_array($value)) {
        $result = getStylishString($marker, $key, $value, $data['indentation']);
    } else {
        $innerContent = getStylish($value, $data['replacer'], $data['spacesCount'], $data['depth'] + 1);
        $result = getStylishInnerContent($marker, $key, $innerContent, $data['indentation']);
    }

    return $result;
}

function getString(mixed $string): string
{
    if (is_bool($string)) {
        return $string ? 'true' : 'false';
    }

    if (is_null($string)) {
        return 'null';
    }

    return $string;
}
