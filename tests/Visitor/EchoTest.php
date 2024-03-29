<?php

namespace Jaunas\PhpCompiler\Tests\Visitor;

use Jaunas\PhpCompiler\Node\Expr\BinaryOp;
use Jaunas\PhpCompiler\Node\Expr\Expr;
use Jaunas\PhpCompiler\Node\Expr\FnCall;
use Jaunas\PhpCompiler\Node\Expr\FunctionCall;
use Jaunas\PhpCompiler\Node\Expr\If_;
use Jaunas\PhpCompiler\Node\Expr\MacroCall;
use Jaunas\PhpCompiler\Node\Expr\StrRef;
use Jaunas\PhpCompiler\Node\Expr\Value\Bool_;
use Jaunas\PhpCompiler\Node\Expr\Value\Number;
use Jaunas\PhpCompiler\Node\Expr\Value\String_;
use Jaunas\PhpCompiler\Node\Factory\PrintFactory;
use Jaunas\PhpCompiler\Node\Fn_;
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
    #[Test]
    #[DataProvider('echoProvider')]
    public function addsPrintToFn(?Expr $expected, Stmt $stmt): void
    {
        $main = new Fn_('main');
        $visitor = new EchoVisitor($main);
        $visitor->enterNode($stmt);

        if ($expected instanceof Expr) {
            $this->assertCount(1, $main->getBody());
            $this->assertEquals($expected, $main->getBody()[0]);
        } else {
            $this->assertEmpty($main->getBody());
        }
    }

    /**
     * @return array<string, array{
     *     expected: ?Expr,
     *     stmt: Stmt
     * }>
     */
    public static function echoProvider(): array
    {
        $trueToBool = new FnCall('to_bool');
        $trueToBool->setSubject(new Bool_(true));

        $concatStrings = new FnCall('concat', [String_::fromString('second string')]);
        $concatStrings->setSubject(String_::fromString('first string'));

        return [
            'no_match' => [
                'expected' => null,
                'stmt' => new PhpFunction('custom_function'),
            ],
            'string' => [
                'expected' => new FunctionCall('Echo', [String_::fromString('Example text')]),
                'stmt' => new PhpEcho([new PhpString('Example text')]),
            ],
            'number' => [
                'expected' => new FunctionCall('Echo', [new Number(5)]),
                'stmt' => new PhpEcho([new PhpInt(5)]),
            ],
            'concat' => [
                'expected' => new FunctionCall('Echo', [$concatStrings]),
                'stmt' => new PhpEcho([new PhpConcat(
                    new PhpString('first string'),
                    new PhpString('second string')
                )]),
            ],
            'comma' => [
                'expected' => new FunctionCall('Echo', [
                    String_::fromString('string'),
                    new Number(5),
                    String_::fromString('string again')
                ]),
                'stmt' => new PhpEcho([
                    new PhpString('string'),
                    new PhpInt(5),
                    new PhpString('string again'),
                ]),
            ],
            'plus' => [
                'expected' => new FunctionCall('Echo', [
                    new BinaryOp('+', new Number(5), new Number(3))
                ]),
                'stmt' => new PhpEcho([new PhpPlus(
                    new PhpInt(5),
                    new PhpInt(3)
                )]),
            ],
            'minus' => [
                'expected' => new FunctionCall('Echo', [
                    new BinaryOp('-', new Number(5), new Number(3))
                ]),
                'stmt' => new PhpEcho([
                    new PhpMinus(
                        new PhpInt(5),
                        new PhpInt(3)
                    ),
                ]),
            ],
            'nested_binary_op' => [
                'expected' => new FunctionCall('Echo', [new BinaryOp(
                    '+',
                    new BinaryOp('+', new Number(3), new Number(4)),
                    new Number(5)
                )]),
                'stmt' => new PhpEcho([
                    new PhpPlus(
                        new PhpPlus(new PhpInt(3), new PhpInt(4)),
                        new PhpInt(5)
                    )
                ]),
            ],
            'ternary_number' => [
                'expected' => new FunctionCall('Echo', [new If_(
                    $trueToBool,
                    new Number(5),
                    new Number(3)
                )]),
                'stmt' => new PhpEcho([new PhpTernary(
                    new PhpConstFetch(new PhpName('true')),
                    new PhpInt(5),
                    new PhpInt(3)
                )]),
            ],
            'ternary_string' => [
                'expected' => new FunctionCall('Echo', [new If_(
                    new Bool_(true),
                    String_::fromString('true'),
                    String_::fromString('false')
                )]),
                'stmt' => new PhpEcho([new PhpTernary(
                    new PhpConstFetch(new PhpName('true')),
                    new PhpString('true'),
                    new PhpString('false')
                )]),
            ],
            'equal' => [
                'expected' => new FunctionCall('Echo', [new If_(
                    new BinaryOp('==', new Number(5), new Number(3)),
                    String_::fromString('equal'),
                    String_::fromString('not equal')
                )]),
                'stmt' => new PhpEcho([new PhpTernary(
                    new PhpEqual(new PhpInt(5), new PhpInt(3)),
                    new PhpString('equal'),
                    new PhpString('not equal')
                )]),
            ]
        ];
    }
}
