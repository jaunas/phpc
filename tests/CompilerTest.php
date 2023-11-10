<?php

namespace Jaunas\PhpCompiler\Tests;

use Jaunas\PhpCompiler\Compiler;
use PhpParser\Node\Stmt\InlineHTML;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Compiler::class)]
class CompilerTest extends TestCase
{
    #[Test]
    public function compilesEmptyScript(): void
    {
        $compiler = new Compiler([]);
        $this->assertStringEqualsFile(__DIR__ . '/fixtures/empty.expected.s', $compiler->compile());
    }

    #[Test]
    public function compilesTextOnlyScript(): void
    {
        $compiler = new Compiler([new InlineHTML(file_get_contents(__DIR__ . '/fixtures/text_only.php'))]);
        $this->assertStringEqualsFile(__DIR__ . '/fixtures/text_only.expected.s', $compiler->compile());
    }

    #[Test]
    public function compilesEchoScript(): void
    {
        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        $ast = $parser->parse(file_get_contents(__DIR__ . '/fixtures/echo.php'));

        $compiler = new Compiler($ast);
        $this->assertStringEqualsFile(__DIR__ . '/fixtures/echo.expected.s', $compiler->compile());
    }
}
