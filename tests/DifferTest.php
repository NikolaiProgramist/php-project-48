<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use function Differ\Differ\Differ\genDiff;
use function PHPUnit\Framework\assertEquals;

class DifferTest extends TestCase
{
    private string $afterJsonPath;
    private string $beforeJsonPath;
    private string $afterYamlPath;
    private string $beforeYamlPath;
    private string $resultPath;

    public function setUp(): void
    {
        $this->beforeJsonPath = $this->getFixturePath('before.json');
        $this->afterJsonPath = $this->getFixturePath('after.json');

        $this->beforeYamlPath = $this->getFixturePath('before.yaml');
        $this->afterYamlPath = $this->getFixturePath('after.yaml');

        $this->resultPath = $this->getFixturePath('result.txt');
    }

    public function getFixturePath(string $fixtureName): string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function testGenDiff(): void
    {
        assertEquals(
            file_get_contents($this->resultPath),
            genDiff(
                $this->beforeJsonPath,
                $this->afterJsonPath
            )
        );

        assertEquals(
            file_get_contents($this->resultPath),
            genDiff(
                $this->beforeYamlPath,
                $this->afterYamlPath
            )
        );
    }
}