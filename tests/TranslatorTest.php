<?php

namespace Jaunas\PhpCompiler\Tests;

use Jaunas\PhpCompiler\Node\Fn_;
use Jaunas\PhpCompiler\Node\MacroCall;
use Jaunas\PhpCompiler\Translator;
use PhpParser\Node\Stmt\InlineHTML;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Translator::class)]
class TranslatorTest extends TestCase
{
    #[Test]
    public function emptyPhpTranslatesToEmptyRust(): void
    {
        $translator = new Translator();

        $main = $translator->translate([]);
        $this->assertInstanceOf(Fn_::class, $main);
        $this->assertEquals('main', $main->getName());
    }

    #[Test]
    public function textTranslatesToPrint(): void
    {
        $translator = new Translator();

        $main = $translator->translate([new InlineHTML('Example text')]);
        $this->assertInstanceOf(Fn_::class, $main);
        $this->assertEquals('main', $main->getName());

        $ast = $main->getBody();
        $this->assertCount(1, $ast);
        $println = $ast[0];

        $this->assertInstanceOf(MacroCall::class, $println);
        $this->assertEquals("print!(\"Example text\");\n", $println->print());
    }
}
