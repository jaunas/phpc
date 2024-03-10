<?php

namespace Jaunas\PhpCompiler\Tests\Visitor;

use Jaunas\PhpCompiler\Node\Expr\BinaryOp;
use Jaunas\PhpCompiler\Node\Expr\FnCall;
use Jaunas\PhpCompiler\Node\Expr\If_;
use Jaunas\PhpCompiler\Node\Expr\StrRef;
use Jaunas\PhpCompiler\Node\Expr\Value\Bool_;
use Jaunas\PhpCompiler\Node\Expr\Value\Number;
use Jaunas\PhpCompiler\Node\Expr\Value\String_;
use Jaunas\PhpCompiler\Node\Factory\PrintFactory;
use Jaunas\PhpCompiler\Node\Fn_;
use Jaunas\PhpCompiler\Node\MacroCall;
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
#[UsesClass(BinaryOp::class)]
#[UsesClass(Bool_::class)]
#[UsesClass(If_::class)]
#[UsesClass(Number::class)]
#[UsesClass(StrRef::class)]
#[UsesClass(PrintFactory::class)]
#[UsesClass(Fn_::class)]
#[UsesClass(MacroCall::class)]
#[UsesClass(String_::class)]
#[UsesClass(FnCall::class)]
class EchoTest extends TestCase
{
    /**
     * @param MacroCall[] $expected
     */
    #[Test]
    #[DataProvider('echoProvider')]
    public function addsPrintToFn(array $expected, Stmt $stmt): void
    {
        $main = new Fn_('main');
        $visitor = new EchoVisitor($main);
        $visitor->enterNode($stmt);

        $this->assertEquals($expected, $main->getBody());
    }

    /**
     * @return array<string, array{
     *     expected: MacroCall[],
     *     stmt: Stmt
     * }>
     */
    public static function echoProvider(): array
    {
        $trueToBool = new FnCall('to_bool');
        $trueToBool->setSubject(new Bool_(true));

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
                'expected' => [PrintFactory::createWithNumberValue(5)],
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
                    PrintFactory::createWithNumberValue(5),
                    PrintFactory::createWithString('string again'),
                ],
                'stmt' => new PhpEcho([
                    new PhpString('string'),
                    new PhpInt(5),
                    new PhpString('string again'),
                ]),
            ],
            'plus' => [
                'expected' => [PrintFactory::createWithExpr(
                    new BinaryOp('+', new Number(5), new Number(3))
                )],
                'stmt' => new PhpEcho([new PhpPlus(
                    new PhpInt(5),
                    new PhpInt(3)
                )]),
            ],
            'minus' => [
                'expected' => [PrintFactory::createWithExpr(
                    new BinaryOp('-', new Number(5), new Number(3))
                )],
                'stmt' => new PhpEcho([
                    new PhpMinus(
                        new PhpInt(5),
                        new PhpInt(3)
                    ),
                ]),
            ],
            'nested_binary_op' => [
                'expected' => [PrintFactory::createWithExpr(
                    new BinaryOp(
                        '+',
                        new BinaryOp('+', new Number(3), new Number(4)),
                        new Number(5)
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
                'expected' => [new MacroCall(
                    'print',
                    new StrRef('{}'),
                    new If_($trueToBool, new Number(5), new Number(3))
                )],
                'stmt' => new PhpEcho([new PhpTernary(
                    new PhpConstFetch(new PhpName('true')),
                    new PhpInt(5),
                    new PhpInt(3)
                )]),
            ],
            'ternary_string' => [
                'expected' => [new MacroCall(
                    'print',
                    StrRef::placeholder(),
                    new If_(new Bool_(true), new StrRef('true'), new StrRef('false'))
                )],
                'stmt' => new PhpEcho([new PhpTernary(
                    new PhpConstFetch(new PhpName('true')),
                    new PhpString('true'),
                    new PhpString('false')
                )]),
            ],
            'equal' => [
                'expected' => [new MacroCall(
                    'print',
                    StrRef::placeholder(),
                    new If_(
                        new BinaryOp('==', new Number(5), new Number(3)),
                        new StrRef('equal'),
                        new StrRef('not equal')
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
