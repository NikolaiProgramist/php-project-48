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
    private string $resultPath2;

    public function setUp(): void
    {
        $this->beforeJsonPath = $this->getFixturePath('before.json');
        $this->afterJsonPath = $this->getFixturePath('after.json');

        $this->before2JsonPath = $this->getFixturePath('before2.json');
        $this->after2JsonPath = $this->getFixturePath('after2.json');

        $this->beforeYamlPath = $this->getFixturePath('before.yaml');
        $this->afterYamlPath = $this->getFixturePath('after.yaml');

        $this->before2YamlPath = $this->getFixturePath('before2.yaml');
        $this->after2YamlPath = $this->getFixturePath('after2.yaml');

        $this->resultPath = $this->getFixturePath('result.txt');
        $this->result2Path = $this->getFixturePath('result2.txt');
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

        assertEquals(
            file_get_contents($this->result2Path),
            genDiff(
                $this->before2JsonPath,
                $this->after2JsonPath
            )
        );

        assertEquals(
            file_get_contents($this->result2Path),
            genDiff(
                $this->before2YamlPath,
                $this->after2YamlPath
            )
        );
    }
}