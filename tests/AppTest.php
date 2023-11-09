<?php

namespace Jaunas\PhpCompiler\Tests;

use Jaunas\PhpCompiler\App;
use Jaunas\PhpCompiler\Exception\FileNotFound;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(App::class)]
class AppTest extends TestCase
{
    private function getFixturePath(string $filename): string
    {
        return sprintf('%s/fixtures/%s', __DIR__, $filename);
    }

    #[Test]
    public function failsWhenNoFile(): void
    {
        $app = new App([1 => $this->getFixturePath('not_exists.php')]);

        $this->expectException(FileNotFound::class);
        $app->compile();
    }

    #[Test]
    public function compilesEmptyScript(): void
    {
        $app = new App([1 => $this->getFixturePath('empty.php')]);
        $app->compile();

        $this->assertFileEquals(
            $this->getFixturePath('empty.expected.s'),
            $this->getFixturePath('empty.s')
        );
    }
}
