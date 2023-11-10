<?php

namespace Jaunas\PhpCompiler\Tests;

use Jaunas\PhpCompiler\Compiler;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Compiler::class)]
class CompilerTest extends TestCase
{
    #[Test]
    #[DataProvider('scriptNameProvider')]
    public function compilesScript(string $scriptName): void
    {
        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        $ast = $parser->parse(file_get_contents($this->getScriptPath($scriptName, 'php')));

        $compiler = new Compiler($ast);
        $this->assertStringEqualsFile($this->getScriptPath($scriptName, 'expected.s'), $compiler->compile());
    }

    public static function scriptNameProvider(): array
    {
        return array_map(function ($filename) {
            return [self::removeExtension($filename)];
        }, self::getPhpFilenames());
    }

    private static function getPhpFilenames(): array|false
    {
        $files = scandir(__DIR__ . '/fixtures/');
        return array_filter($files, function ($filename) {
            return preg_match('/\\.php$/', $filename);
        });
    }

    private static function removeExtension(mixed $filename): string
    {
        return substr($filename, 0, strrpos($filename, "."));
    }

    public function getScriptPath(string $scriptName, string $extension): string
    {
        return sprintf("%s/fixtures/%s.%s", __DIR__, $scriptName, $extension);
    }
}
