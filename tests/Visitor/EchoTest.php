<?php

namespace Jaunas\PhpCompiler\Tests\Visitor;

use Jaunas\PhpCompiler\Node\Expr\Number as RustNumber;
use Jaunas\PhpCompiler\Node\Expr\String_ as RustString;
use Jaunas\PhpCompiler\Node\Fn_ as RustFn;
use Jaunas\PhpCompiler\Node\MacroCall;
use Jaunas\PhpCompiler\Node\MacroCall as RustMacroCall;
use Jaunas\PhpCompiler\Visitor\Echo_;
use PhpParser\Node\Expr\BinaryOp\Concat as PhpConcat;
use PhpParser\Node\Expr\BinaryOp\Plus as PhpPlus;
use PhpParser\Node\Scalar\LNumber as PhpLNumber;
use PhpParser\Node\Scalar\String_ as PhpString;
use PhpParser\Node\Stmt\Echo_ as PhpEcho;
use PhpParser\Node\Stmt\Function_ as PhpFunction;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Echo_::class)]
#[UsesClass(RustString::class)]
#[UsesClass(RustFn::class)]
#[UsesClass(RustMacroCall::class)]
#[UsesClass(RustNumber::class)]
class EchoTest extends TestCase
{
    #[Test]
    public function doesNothingIfDontMatch(): void
    {
        $main = new RustFn('main');

        $visitor = new Echo_($main);
        $visitor->enterNode(new PhpFunction('custom_function'));

        $this->assertEmpty($main->getBody());
    }

    /**
     * @param string[] $expected
     */
    #[Test]
    #[DataProvider('echoProvider')]
    public function addsPrintToFn(array $expected, PhpEcho $echo): void
    {
        $main = new RustFn('main');

        $visitor = new Echo_($main);
        $visitor->enterNode($echo);

        $this->assertCount(count($expected), $main->getBody());
        $print = array_map(
            function (MacroCall $node) {
                return $node->print();
            },
            $main->getBody()
        );
        $this->assertSame($expected, $print);
    }

    /**
     * @return array<string, array{
     *     expected: string[],
     *     echo: PhpEcho
     * }>
     */
    public static function echoProvider(): array
    {
        return [
            'string' => [
                'expected' => ["print!(\"Example text\");\n"],
                'echo' => new PhpEcho([new PhpString('Example text')]),
            ],
            'number' => [
                'expected' => ["print!(\"{}\", 5);\n"],
                'echo' => new PhpEcho([new PhpLNumber(5)]),
            ],
            'concat' => [
                'expected' => ["print!(\"first string\");\n", "print!(\"second string\");\n"],
                'echo' => new PhpEcho([new PhpConcat(
                    new PhpString('first string'),
                    new PhpString('second string')
                )]),
            ],
            'comma' => [
                'expected' => ["print!(\"string\");\n", "print!(\"{}\", 5);\n", "print!(\"string again\");\n"],
                'echo' => new PhpEcho([
                    new PhpString('string'),
                    new PhpLNumber(5),
                    new PhpString('string again'),
                ]),
            ],
            'plus' => [
                'expected' => ["print!(\"{}\", 5 + 3);\n"],
                'echo' => new PhpEcho([new PhpPlus(
                    new PhpLNumber(5),
                    new PhpLNumber(3)
                )]),
            ],
        ];
    }
}
