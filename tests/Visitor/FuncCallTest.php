<?php

namespace Jaunas\PhpCompiler\Tests\Visitor;

use Jaunas\PhpCompiler\Node\Expr\FunctionCall;
use Jaunas\PhpCompiler\Node\Expr\Value\Bool_;
use Jaunas\PhpCompiler\Node\Expr\Value\String_;
use Jaunas\PhpCompiler\Node\Fn_;
use Jaunas\PhpCompiler\Visitor\FuncCall;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall as PhpFuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_ as PhpString;
use PhpParser\Node\Stmt\Echo_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FuncCall::class)]
class FuncCallTest extends TestCase
{
    #[Test]
    #[DataProvider('functionCallProvider')]
    public function addsFunctionCallToFn(?FunctionCall $expected, Node $node): void
    {
        $main = new Fn_('main');

        $visitor = new FuncCall($main);
        $visitor->enterNode($node);

        if ($expected instanceof FunctionCall) {
            $this->assertCount(1, $main->getBody());
            $this->assertEquals($expected, $main->getBody()[0]);
        } else {
            $this->assertEmpty($main->getBody());
        }
    }

    /**
     * @return array<array{expected: ?FunctionCall, node: Node}>
     */
    public static function functionCallProvider(): array
    {
        return [
            [
                'expected' => null,
                'node' => new Echo_([]),
            ],
            [
                'expected' => null,
                'node' => new PhpFuncCall(new Name('not_existing'), []),
            ],
            [
                'expected' => new FunctionCall('VarDump', [new Bool_(true)]),
                'node' => new PhpFuncCall(new Name('var_dump'), [new Arg(new ConstFetch(new Name('true')))]),
            ],
            [
                'expected' => new FunctionCall('FileExists', [String_::fromString('test.txt')]),
                'node' => new PhpFuncCall(new Name('file_exists'), [new Arg(new PhpString('test.txt'))]),
            ],
        ];
    }
}
