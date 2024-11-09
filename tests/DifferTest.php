<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Differ\Differ\genDiff;
use function PHPUnit\Framework\assertEquals;

class DifferTest extends TestCase
{
    private string $afterJsonPath;
    private string $beforeJsonPath;
    private string $afterYamlPath;
    private string $beforeYamlPath;

    public function setUp(): void
    {
        $this->beforeJsonPath = $this->getFixturePath('before.json');
        $this->afterJsonPath = $this->getFixturePath('after.json');

        $this->beforeYamlPath = $this->getFixturePath('before.yaml');
        $this->afterYamlPath = $this->getFixturePath('after.yaml');
    }

    public function getFixturePath(string $fixtureName): string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    #[DataProvider('genDiffProvider')]
    public function testGenDiff($resultName, $format): void
    {
        assertEquals(
            file_get_contents($this->getFixturePath($resultName)),
            genDiff($this->beforeJsonPath, $this->afterJsonPath, $format)
        );

        assertEquals(
            file_get_contents($this->getFixturePath($resultName)),
            genDiff($this->beforeYamlPath, $this->afterYamlPath, $format)
        );
    }

    public static function genDiffProvider(): array
    {
        return [
            ['resultStylish.txt', 'stylish'],
            ['resultPlain.txt', 'plain'],
            ['resultJson.txt', 'json']
        ];
    }
}
