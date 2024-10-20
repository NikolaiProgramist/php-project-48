<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;
use function PHPUnit\Framework\assertEquals;

class DifferTest extends TestCase
{
    private string $firstFileContent;
    private string $secondFileContent;
    private string $resultFileContent;

    public function setUp(): void
    {
        $this->firstFileContent = $this->getFixtureContent('before.json');
        $this->secondFileContent = $this->getFixtureContent('after.json');
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
                'firstFileContent' => $this->firstFileContent,
                'secondFileContent' => $this->secondFileContent
            ])
        );
    }
}