<?php

namespace Jaunas\PhpCompiler\Tests;

use Jaunas\PhpCompiler\Node\Fn_;
use Jaunas\PhpCompiler\Translator;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Stmt\InlineHTML;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
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
                'inputAst' => [new InlineHTML('Example text')],
            ],
            'echo' => [
                'expected' => ["print!(\"Example text\");\n"],
                'inputAst' => [
                    new Echo_([
                        new String_('Example text')
                    ]),
                ],
            ],
            'echo_integer' => [
                'expected' => ["print!(\"{}\", 314159);\n"],
                'inputAst' => [
                    new Echo_([
                        new LNumber(314159),
                    ])
                ],
            ],
            'concat' => [
                'expected' => ["print!(\"first string\");\n", "print!(\"second string\");\n"],
                'inputAst' => [
                    new Echo_([
                        new Concat(
                            new String_('first string'),
                            new String_('second string')
                        ),
                    ])
                ],
            ],
        ];
    }
}
