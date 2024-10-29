<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;
use function PHPUnit\Framework\assertEquals;

class DifferTest extends TestCase
{
    private string $afterJsonPath;
    private string $beforeJsonPath;
    private string $afterYamlPath;
    private string $beforeYamlPath;
    private string $resultStylishPath;
    private string $resultPlainPath;
    private string $resultJsonPath;

    public function setUp(): void
    {
        $this->beforeJsonPath = $this->getFixturePath('before.json');
        $this->afterJsonPath = $this->getFixturePath('after.json');

        $this->beforeYamlPath = $this->getFixturePath('before.yaml');
        $this->afterYamlPath = $this->getFixturePath('after.yaml');

        $this->resultStylishPath = $this->getFixturePath('resultStylish.txt');
        $this->resultPlainPath = $this->getFixturePath('resultPlain.txt');
        $this->resultJsonPath = $this->getFixturePath('resultJson.txt');
    }

    public function getFixturePath(string $fixtureName): string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function testGenDiff(): void
    {
        assertEquals(
            file_get_contents($this->resultStylishPath),
            genDiff(
                $this->beforeJsonPath,
                $this->afterJsonPath
            )
        );

        assertEquals(
            file_get_contents($this->resultStylishPath),
            genDiff(
                $this->beforeYamlPath,
                $this->afterYamlPath
            )
        );

        assertEquals(
            file_get_contents($this->resultPlainPath),
            genDiff(
                $this->beforeJsonPath,
                $this->afterJsonPath,
                'plain'
            )
        );

        assertEquals(
            file_get_contents($this->resultPlainPath),
            genDiff(
                $this->beforeYamlPath,
                $this->afterYamlPath,
                'plain'
            )
        );

        assertEquals(
            file_get_contents($this->resultJsonPath),
            genDiff(
                $this->beforeYamlPath,
                $this->afterYamlPath,
                'json'
            )
        );

        assertEquals(
            file_get_contents($this->resultJsonPath),
            genDiff(
                $this->beforeJsonPath,
                $this->afterJsonPath,
                'json'
            )
        );
    }
}
