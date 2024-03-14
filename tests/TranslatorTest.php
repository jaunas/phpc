<?php

namespace Jaunas\PhpCompiler\Tests;

use Jaunas\PhpCompiler\Node\Expr\MacroCall;
use Jaunas\PhpCompiler\Node\Expr\StrRef;
use Jaunas\PhpCompiler\Node\Fn_;
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

        $main = $translator->translate([]);
        $this->assertInstanceOf(Fn_::class, $main);
        $this->assertEquals('main', $main->getName());
    }

    /**
     * @param string[] $expected
     */
    #[Test]
    #[DataProvider('stringToPrintProvider')]
    public function echoTranslatesToPrint(array $expected, Stmt $rootAst): void
    {
        $translator = new Translator();
        $ast = $translator->translate([$rootAst])->getBody();
        $this->assertCount(count($expected), $ast);

        $prints = [];
        foreach ($ast as $print) {
            $prints[] = $print->getSource();
        }

        $this->assertEquals($expected, $prints);
    }

    /**
     * @return array<string, array{
     *     expected: string[],
     *     rootAst: Stmt
     * }>
     */
    public static function stringToPrintProvider(): array
    {
        return [
            'text_only' => [
                'expected' => ["print!(\"Example text\")"],
                'rootAst' => new PhpInlineHtml('Example text'),
            ],
        ];
    }
}
