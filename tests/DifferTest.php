<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;
use function PHPUnit\Framework\assertEquals;

class DifferTest extends TestCase
{
    private string $firstContentJson;
    private string $secondContentJson;
    private string $firstContentYaml;
    private string $secondContentYaml;
    private string $resultFileContent;

    public function setUp(): void
    {
        $this->firstContentJson = $this->getFixtureContent('before.json');
        $this->secondContentJson = $this->getFixtureContent('after.json');

        $this->firstContentYaml = $this->getFixtureContent('before.yaml');
        $this->secondContentYaml = $this->getFixtureContent('after.yaml');

        $this->resultFileContent = $this->getFixtureContent('result.txt');
    }

    public function getFixtureContent(string $fixtureName): string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return file_get_contents(realpath(implode('/', $parts)));
    }

    public function testGenDiff(): void
    {
        assertEquals(
            $this->resultFileContent,
            genDiff([
                'firstFileContent' => $this->firstContentJson,
                'secondFileContent' => $this->secondContentJson
            ])
        );

        assertEquals(
            $this->resultFileContent,
            genDiff([
                'firstFileContent' => $this->firstContentYaml,
                'secondFileContent' => $this->secondContentYaml
            ])
        );
    }
}