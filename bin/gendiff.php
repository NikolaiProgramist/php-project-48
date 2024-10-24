#!/usr/bin/env php

<?php

$autoloadPathLocal = __DIR__ . '/../vendor/autoload.php';
$autoloadPathGlobal = __DIR__ . '/../../../autoload.php';

if (file_exists($autoloadPathLocal)) {
    require_once $autoloadPathLocal;
} else {
    require_once $autoloadPathGlobal;
}

use function cli\line;
use function Differ\Parsers\Parser\Parse;

$doc = <<<DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: stylish]
DOC;

$argv = Docopt::handle($doc, array('version' => 'Gendiff 1.0'));

line(parse($argv['<firstFile>'], $argv['<secondFile>']));
