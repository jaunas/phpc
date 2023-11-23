<?php

namespace Jaunas\PhpCompiler\Tests\Visitor;

use Jaunas\PhpCompiler\Node\Fn_;
use Jaunas\PhpCompiler\Visitor\Echo_;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Echo_ as EchoNode;
use PhpParser\Node\Stmt\Function_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Echo_::class)]
class EchoTest extends TestCase
{
    #[Test]
    public function doesNothingIfDontMatch(): void
    {
        $main = new Fn_('main');

        $visitor = new Echo_($main);
        $visitor->enterNode(new Function_('custom_function'));

        $this->assertEmpty($main->getBody());
    }

    #[Test]
    public function addsPrintToFn(): void
    {
        $main = new Fn_('main');

        $visitor = new Echo_($main);
        $visitor->enterNode(new EchoNode([new String_('Example text')]));

        $this->assertCount(1, $main->getBody());
        $print = $main->getBody()[0];
        $this->assertEquals("print!(\"Example text\");\n", $print->print());
    }

    #[Test]
    public function addsPrintNumberToFn(): void
    {
        $main = new Fn_('main');

        $visitor = new Echo_($main);
        $visitor->enterNode(new EchoNode([new LNumber(5)]));

        $this->assertCount(1, $main->getBody());
        $print = $main->getBody()[0];
        $this->assertEquals("print!(\"{}\", 5);\n", $print->print());
    }

    #[Test]
    public function addsPrintConcatToFn(): void
    {
        $main = new Fn_('main');

        $visitor = new Echo_($main);
        $visitor->enterNode(
            new EchoNode([
                new Concat(
                    new String_('first string'),
                    new String_('second string')
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
