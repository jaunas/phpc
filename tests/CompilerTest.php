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
    use ScriptNameProvider;

    #[Test]
    #[DataProvider('scriptNameProvider')]
    public function compilesScript(string $scriptName): void
    {
        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        $ast = $parser->parse(file_get_contents($this->getScriptPath($scriptName, 'php')));

        $compiler = new Compiler($ast);
        $this->assertStringEqualsFile($this->getScriptPath($scriptName, 'expected.s'), $compiler->compile());
    }
}
