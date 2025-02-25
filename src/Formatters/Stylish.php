<?php

namespace Differ\Formatters\Stylish;

enum Marker: string
{
    case ADD = '+';
    case REMOVE = '-';
    case UNCHANGED = ' ';
}

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
            $statusName = $tree[$key]['status'] ?? 'add';
            $resultString = $acc;

            $status = match ($statusName) {
                'add' => Marker::ADD->value,
                'remove' => Marker::REMOVE->value,
                default => Marker::UNCHANGED->value
            };

            $indentationCount = $spacesCount * $depth - 2;
            $indentation = str_repeat($replacer, $indentationCount);

            if (array_key_exists('children', $keyData)) {
                $value = $keyData['children'];

                if ($status === Marker::ADD->value) {
                    $innerContent = getArrayContent($value, $replacer, $spacesCount, $depth + 1);
                    $string = getStylishInnerContent($status, $key, $innerContent, $indentation);
                    return "{$resultString}{$string}";
                }

                $innerContent = getStylish($value, $replacer, $spacesCount, $depth + 1);
                $string = getStylishInnerContent($status, $key, $innerContent, $indentation);
                return "{$resultString}{$string}";
            }

            if (array_key_exists('value', $keyData)) {
                $value = $keyData['value'];
                $string = getStylishString($status, $key, $value, $indentation);
                return "{$resultString}{$string}";
            }

            $data = [
                'indentation' => $indentation,
                'replacer' => $replacer,
                'spacesCount' => $spacesCount,
                'depth' => $depth
            ];

            $stringBefore = getChangedString(
                Marker::REMOVE->value,
                $keyData['beforeValue'],
                $key,
                $data
            );

            $stringAfter = getChangedString(
                Marker::ADD->value,
                $keyData['afterValue'],
                $key,
                $data
            );

            return "{$resultString}{$stringBefore}{$stringAfter}";
        },
        ''
    );
}

function getArrayContent(array $tree, string $replacer, int $spacesCount, int $depth): string
{
    return array_reduce(array_keys($tree), function ($acc, $key) use ($tree, $replacer, $spacesCount, $depth) {
        $indentationCount = $spacesCount * $depth - 2;
        $indentation = str_repeat($replacer, $indentationCount);
        $resultString = $acc;

        if (array_key_exists('value', $tree[$key])) {
            $value = $tree[$key]['value'];
            $string = getStylishString(Marker::UNCHANGED->value, $key, $value, $indentation);
            return "{$resultString}{$string}";
        }

        $value = $tree[$key]['children'];
        $innerContent = getArrayContent($value, $replacer, $spacesCount, $depth + 1);
        return getStylishInnerContent(Marker::UNCHANGED->value, $key, $innerContent, $indentation);
    }, '');
}

function getStylishInnerContent(string $marker, int|string $key, string $innerContent, string $indentation): string
{
    return "{$indentation}{$marker} {$key}: {\n{$innerContent}{$indentation}  }\n";
}

function getStylishString(string $marker, int|string $key, mixed $value, string $indentation): string
{
    $keyValue = getString($value);
    return "{$indentation}{$marker} {$key}: {$keyValue}\n";
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
