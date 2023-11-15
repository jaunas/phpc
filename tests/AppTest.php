<?php

namespace Jaunas\PhpCompiler\Tests;

use Jaunas\PhpCompiler\App;
use Jaunas\PhpCompiler\Exception\FileNotFound;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(App::class)]
class AppTest extends TestCase
{
    use ScriptNameProvider;

    private function getFixturePath(string $filename): string
    {
        return sprintf('%s/fixtures/%s', __DIR__, $filename);
    }

    #[Test]
    public function failsWhenNoFile(): void
    {
        $app = new App([1 => $this->getFixturePath('not_exists.php')]);

        $this->expectException(FileNotFound::class);
        $app->generateCompiledScript();
    }

    #[Test]
    public function generatesCompiledScript(): void
    {
        $app = new App([1 => $this->getFixturePath('empty.php')]);
        $app->generateCompiledScript();

        $this->assertFileEquals(
            $this->getFixturePath('empty.expected.s'),
            $this->getFixturePath('empty.s')
        );

        unlink($this->getFixturePath('empty.s'));
    }

    #[Test]
    #[DataProvider('scriptNameProvider')]
    public function outputsMatch(string $scriptName): void
    {
        $expectedOutput = exec(sprintf('php %s', $this->getScriptPath($scriptName, 'php')));
        $output = $this->getCompiledOutput($scriptName);
        $this->assertEquals($expectedOutput, $output);
    }

    private function getCompiledOutput(string $scriptName): string
    {
        $phpScriptPath = $this->getScriptPath($scriptName, 'php');
        $asmScriptPath = $this->getScriptPath($scriptName, 's');

        $app = new App([1 => $phpScriptPath]);
        $app->generateCompiledScript();
        $output = exec(sprintf('gcc -nostartfiles %s -o tmp && ./tmp', $asmScriptPath));
        unlink($asmScriptPath);
        unlink(__DIR__ . '/tmp');

        return $output;
    }
}
