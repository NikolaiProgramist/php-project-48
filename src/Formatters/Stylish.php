<?php

namespace Differ\Formatters\Stylish;

function stylish($tree, string $replacer = ' ', int $spacesCount = 4, int $depth = 1): string
{
    $child = array_reduce(
        array_keys($tree),
        function ($acc, $key) use ($tree, $replacer, $spacesCount, $depth) {
            $keyName = $key;
            $indentationCount = $spacesCount * $depth - 2;
            $indentation = str_repeat($replacer, $indentationCount);

            $markers = ['+', '-', ' '];
            $marker = ' ';

            if (in_array($key[0], $markers)) {
                $marker = $key[0];
                $keyName = substr($key, 1);
            }

            if (!is_array($tree[$key])) {
                $value = toString($tree[$key]);
                $value = $value === '' ? '' : " {$value}";
                $acc .= $indentation . "{$marker} {$keyName}:{$value}\n";
                return $acc;
            }

            $innerContent = stylish($tree[$key], $replacer, $spacesCount, $depth + 1);
            $acc .= $indentation . "{$marker} {$keyName}: {\n{$innerContent}";
            $acc .= $indentation . "  }\n";
            return $acc;
        },
        ''
    );

    if ($depth === 1) {
        return "{\n{$child}}";
    }

    return "{$child}";
}

function toString($string): string
{
    if (is_bool($string)) {
        return $string ? 'true' : 'false';
    }

    if (is_null($string)) {
        return 'null';
    }

    return $string;
}
