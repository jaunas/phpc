<?php

namespace Jaunas\PhpCompiler\Tests;

use Jaunas\PhpCompiler\Node\Expr\Number as RustNumber;
use Jaunas\PhpCompiler\Node\Expr\String_ as RustString;
use Jaunas\PhpCompiler\Node\Fn_ as RustFn;
use Jaunas\PhpCompiler\Node\MacroCall as RustMacroCall;
use Jaunas\PhpCompiler\Translator;
use Jaunas\PhpCompiler\Visitor\Echo_ as EchoVisitor;
use Jaunas\PhpCompiler\Visitor\InlineHtml as InlineHtmlVisitor;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Scalar\LNumber as PhpLNumber;
use PhpParser\Node\Scalar\String_ as PhpString;
use PhpParser\Node\Stmt\Echo_ as PhpEcho;
use PhpParser\Node\Stmt\InlineHTML as PhpInlineHtml;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Translator::class)]
#[UsesClass(RustFn::class)]
#[UsesClass(RustString::class)]
#[UsesClass(RustMacroCall::class)]
#[UsesClass(RustNumber::class)]
#[UsesClass(EchoVisitor::class)]
#[UsesClass(InlineHtmlVisitor::class)]
class TranslatorTest extends TestCase
{
    #[Test]
    public function emptyPhpTranslatesToEmptyRust(): void
    {
        $translator = new Translator();

        $main = $translator->translate([]);
        $this->assertInstanceOf(RustFn::class, $main);
        $this->assertEquals('main', $main->getName());
    }

    #[Test]
    #[DataProvider('stringToPrintProvider')]
    public function echoTranslatesToPrint(array $expected, array $inputAst): void
    {
        $translator = new Translator();

        $ast = $translator->translate($inputAst)->getBody();

        $this->assertCount(count($expected), $ast);
        $prints = [];
        foreach ($ast as $print) {
            $prints[] = $print->print();
        }
        $this->assertEquals($expected, $prints);
    }

    public static function stringToPrintProvider(): array
    {
        return [
            'text_only' => [
                'expected' => ["print!(\"Example text\");\n"],
                'inputAst' => [new PhpInlineHtml('Example text')],
            ],
            'echo' => [
                'expected' => ["print!(\"Example text\");\n"],
                'inputAst' => [
                    new PhpEcho([
                        new PhpString('Example text')
                    ]),
                ],
            ],
            'echo_integer' => [
                'expected' => ["print!(\"{}\", 314159);\n"],
                'inputAst' => [
                    new PhpEcho([
                        new PhpLNumber(314159),
                    ])
                ],
            ],
            'concat' => [
                'expected' => ["print!(\"first string\");\n", "print!(\"second string\");\n"],
                'inputAst' => [
                    new PhpEcho([
                        new Concat(
                            new PhpString('first string'),
                            new PhpString('second string')
                        ),
                    ])
                ],
            ],
        ];
    }
}
