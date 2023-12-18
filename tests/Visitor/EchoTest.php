<?php

namespace Jaunas\PhpCompiler\Tests\Visitor;

use Jaunas\PhpCompiler\Node\Expr\BinaryOp as RustBinaryOp;
use Jaunas\PhpCompiler\Node\Expr\Bool_ as RustBool;
use Jaunas\PhpCompiler\Node\Expr\If_ as RustIf;
use Jaunas\PhpCompiler\Node\Expr\Number as RustNumber;
use Jaunas\PhpCompiler\Node\Expr\PhpNumber as RustPhpNumber;
use Jaunas\PhpCompiler\Node\Expr\String_ as RustString;
use Jaunas\PhpCompiler\Node\Factory\PrintFactory;
use Jaunas\PhpCompiler\Node\Fn_ as RustFn;
use Jaunas\PhpCompiler\Node\MacroCall as RustMacroCall;
use Jaunas\PhpCompiler\Visitor\Echo_ as EchoVisitor;
use PhpParser\Node\Expr\BinaryOp\Concat as PhpConcat;
use PhpParser\Node\Expr\BinaryOp\Equal as PhpEqual;
use PhpParser\Node\Expr\BinaryOp\Minus as PhpMinus;
use PhpParser\Node\Expr\BinaryOp\Plus as PhpPlus;
use PhpParser\Node\Expr\ConstFetch as PhpConstFetch;
use PhpParser\Node\Expr\Ternary as PhpTernary;
use PhpParser\Node\Name as PhpName;
use PhpParser\Node\Scalar\Int_ as PhpInt;
use PhpParser\Node\Scalar\String_ as PhpString;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Echo_ as PhpEcho;
use PhpParser\Node\Stmt\Function_ as PhpFunction;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EchoVisitor::class)]
#[UsesClass(RustString::class)]
#[UsesClass(RustFn::class)]
#[UsesClass(RustMacroCall::class)]
#[UsesClass(RustNumber::class)]
#[UsesClass(RustBinaryOp::class)]
#[UsesClass(RustPhpNumber::class)]
#[UsesClass(PrintFactory::class)]
class EchoTest extends TestCase
{
    /**
     * @param RustMacroCall[] $expected
     */
    #[Test]
    #[DataProvider('echoProvider')]
    public function addsPrintToFn(array $expected, Stmt $stmt): void
    {
        $main = new RustFn('main');
        $visitor = new EchoVisitor($main);
        $visitor->enterNode($stmt);

        $this->assertEquals($expected, $main->getBody());
    }

    /**
     * @return array<string, array{
     *     expected: RustMacroCall[],
     *     stmt: Stmt
     * }>
     */
    public static function echoProvider(): array
    {
        return [
            'no_match' => [
                'expected' => [],
                'stmt' => new PhpFunction('custom_function'),
            ],
            'string' => [
                'expected' => [PrintFactory::createWithString('Example text')],
                'stmt' => new PhpEcho([new PhpString('Example text')]),
            ],
            'number' => [
                'expected' => [PrintFactory::createWithNumber(5)],
                'stmt' => new PhpEcho([new PhpInt(5)]),
            ],
            'concat' => [
                'expected' => [
                    PrintFactory::createWithString('first string'),
                    PrintFactory::createWithString('second string'),
                ],
                'stmt' => new PhpEcho([new PhpConcat(
                    new PhpString('first string'),
                    new PhpString('second string')
                )]),
            ],
            'comma' => [
                'expected' => [
                    PrintFactory::createWithString('string'),
                    PrintFactory::createWithNumber(5),
                    PrintFactory::createWithString('string again'),
                ],
                'stmt' => new PhpEcho([
                    new PhpString('string'),
                    new PhpInt(5),
                    new PhpString('string again'),
                ]),
            ],
            'plus' => [
                'expected' => [PrintFactory::createWithNumber(
                    new RustBinaryOp('+', new RustNumber(5), new RustNumber(3))
                )],
                'stmt' => new PhpEcho([new PhpPlus(
                    new PhpInt(5),
                    new PhpInt(3)
                )]),
            ],
            'minus' => [
                'expected' => [PrintFactory::createWithNumber(
                    new RustBinaryOp('-', new RustNumber(5), new RustNumber(3))
                )],
                'stmt' => new PhpEcho([
                    new PhpMinus(
                        new PhpInt(5),
                        new PhpInt(3)
                    ),
                ]),
            ],
            'nested_binary_op' => [
                'expected' => [PrintFactory::createWithNumber(
                    new RustBinaryOp(
                        '+',
                        new RustBinaryOp('+', new RustNumber(3), new RustNumber(4)),
                        new RustNumber(5)
                    )
                )],
                'stmt' => new PhpEcho([
                    new PhpPlus(
                        new PhpPlus(new PhpInt(3), new PhpInt(4)),
                        new PhpInt(5)
                    )
                ]),
            ],
            'ternary_number' => [
                'expected' => [new RustMacroCall(
                    'print',
                    new RustString('{}'),
                    new RustIf(new RustBool(true), new RustNumber(5), new RustNumber(3))
                )],
                'stmt' => new PhpEcho([new PhpTernary(
                    new PhpConstFetch(new PhpName('true')),
                    new PhpInt(5),
                    new PhpInt(3)
                )]),
            ],
            'ternary_string' => [
                'expected' => [new RustMacroCall(
                    'print',
                    new RustString('{}'),
                    new RustIf(new RustBool(true), new RustString('true'), new RustString('false'))
                )],
                'stmt' => new PhpEcho([new PhpTernary(
                    new PhpConstFetch(new PhpName('true')),
                    new PhpString('true'),
                    new PhpString('false')
                )]),
            ],
            'equal' => [
                'expected' => [new RustMacroCall(
                    'print',
                    new RustString('{}'),
                    new RustIf(
                        new RustBinaryOp('==', new RustNumber(5), new RustNumber(3)),
                        new RustString('equal'),
                        new RustString('not equal')
                    )
                )],
                'stmt' => new PhpEcho([new PhpTernary(
                    new PhpEqual(new PhpInt(5), new PhpInt(3)),
                    new PhpString('equal'),
                    new PhpString('not equal')
                )]),
            ]
        ];
    }
}
