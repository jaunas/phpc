<?php

namespace Jaunas\PhpCompiler\Tests\Visitor;

use Jaunas\PhpCompiler\Node\Expr\Number as RustNumber;
use Jaunas\PhpCompiler\Node\Expr\String_ as RustString;
use Jaunas\PhpCompiler\Node\Fn_ as RustFn;
use Jaunas\PhpCompiler\Node\MacroCall as RustMacroCall;
use Jaunas\PhpCompiler\Visitor\Echo_;
use PhpParser\Node\Expr\BinaryOp\Concat as PhpConcat;
use PhpParser\Node\Scalar\LNumber as PhpLNumber;
use PhpParser\Node\Scalar\String_ as PhpString;
use PhpParser\Node\Stmt\Echo_ as PhpEcho;
use PhpParser\Node\Stmt\Function_ as PhpFunction;
use PHPUnit\Framework\Attributes\CoversClass;
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

    #[Test]
    public function addsPrintToFn(): void
    {
        $main = new RustFn('main');

        $visitor = new Echo_($main);
        $visitor->enterNode(new PhpEcho([new PhpString('Example text')]));

        $this->assertCount(1, $main->getBody());
        $print = $main->getBody()[0];
        $this->assertEquals("print!(\"Example text\");\n", $print->print());
    }

    #[Test]
    public function addsPrintNumberToFn(): void
    {
        $main = new RustFn('main');

        $visitor = new Echo_($main);
        $visitor->enterNode(new PhpEcho([new PhpLNumber(5)]));

        $this->assertCount(1, $main->getBody());
        $print = $main->getBody()[0];
        $this->assertEquals("print!(\"{}\", 5);\n", $print->print());
    }

    #[Test]
    public function addsPrintConcatToFn(): void
    {
        $main = new RustFn('main');

        $visitor = new Echo_($main);
        $visitor->enterNode(
            new PhpEcho([
                new PhpConcat(
                    new PhpString('first string'),
                    new PhpString('second string')
                ),
            ])
        );

        $this->assertCount(2, $main->getBody());
        $prints = [
            $main->getBody()[0]->print(),
            $main->getBody()[1]->print()
        ];
        $this->assertEquals(
            "print!(\"first string\");\nprint!(\"second string\");\n",
            implode('', $prints)
        );
    }
}
