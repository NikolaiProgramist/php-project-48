# Differ

[![Actions Status](https://github.com/NikolaiProgramist/php-project-48/actions/workflows/hexlet-check.yml/badge.svg)](https://github.com/NikolaiProgramist/php-project-48/actions) [![tests-check](https://github.com/NikolaiProgramist/php-project-48/actions/workflows/tests-check.yml/badge.svg)](https://github.com/NikolaiProgramist/php-project-48/actions/workflows/tests-check.yml) [![Maintainability](https://api.codeclimate.com/v1/badges/b070d4d02aad3ce48e32/maintainability)](https://codeclimate.com/github/NikolaiProgramist/php-project-48/maintainability) [![Test Coverage](https://api.codeclimate.com/v1/badges/b070d4d02aad3ce48e32/test_coverage)](https://codeclimate.com/github/NikolaiProgramist/php-project-48/test_coverage)

## About

This project is a console utility that finds the difference between two files.
This utility can work with such data formats as: **JSON**, **YAML**, **YML**.
You can also see the comparison result in different output formats, for example: **stylish**, **plain**, **json**.
The default output format is **stylish**.
If the files do not exist, the utility will notify you about it.

## Prerequisites

+ Linux, MacOS, WSL
+ PHP >=8.3
+ Composer
+ Make
+ Git

## Install

```bash
git clone https://github.com/NikolaiProgramist/php-project-48.git
cd php-project-48
make install
```

## Run

```bash
php bin/gendiff --format=stylish file1.json file2.json
```

## Examples in asciinema:

#### JSON

[![asciicast](https://asciinema.org/a/681719.svg)](https://asciinema.org/a/681719)

#### YAML

[![asciicast](https://asciinema.org/a/682331.svg)](https://asciinema.org/a/682331)

#### Nested YAML (stylish format)

[![asciicast](https://asciinema.org/a/684444.svg)](https://asciinema.org/a/684444)

#### Nested YAML (plain format)

[![asciicast](https://asciinema.org/a/haPb0VNshyI0vluxmI3e4ScgQ.svg)](https://asciinema.org/a/haPb0VNshyI0vluxmI3e4ScgQ)

#### JSON of Differ (json format)

[![asciicast](https://asciinema.org/a/FHSVCs1esQv4I5wXrA8Zo5X5r.svg)](https://asciinema.org/a/FHSVCs1esQv4I5wXrA8Zo5X5r)
