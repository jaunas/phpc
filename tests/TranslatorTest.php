<?php

namespace Jaunas\PhpCompiler\Tests;

use Jaunas\PhpCompiler\Node\Expr\MacroCall;
use Jaunas\PhpCompiler\Node\Expr\StrRef;
use Jaunas\PhpCompiler\Node\Fn_;
use Jaunas\PhpCompiler\Node\Mod;
use Jaunas\PhpCompiler\Translator;
use Jaunas\PhpCompiler\Visitor\Echo_ as EchoVisitor;
use Jaunas\PhpCompiler\Visitor\InlineHtml as InlineHtmlVisitor;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\InlineHTML as PhpInlineHtml;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Translator::class)]
#[UsesClass(Fn_::class)]
#[UsesClass(StrRef::class)]
#[UsesClass(MacroCall::class)]
#[UsesClass(EchoVisitor::class)]
#[UsesClass(InlineHtmlVisitor::class)]
class TranslatorTest extends TestCase
{
    #[Test]
    public function emptyPhpTranslatesToEmptyRust(): void
    {
        $translator = new Translator();

        $mainMod = $translator->translate([]);
        $this->assertInstanceOf(Mod::class, $mainMod);

        $expected = "use rust_php::*;\nuse rust_php::functions::Function;\nfn main() {\n}\n";
        $this->assertEquals($expected, $mainMod->getSource());
    }

    #[Test]
    public function echoTranslatesToPrint(): void
    {
        $translator = new Translator();
        $source = $translator->translate([new PhpInlineHtml('Example text')])->getSource();

        $expected = "use rust_php::*;\nuse rust_php::functions::Function;\nfn main() {\nprint!(\"Example text\");\n}\n";
        $this->assertEquals($expected, $source);
    }
}
