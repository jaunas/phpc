<?php

namespace Jaunas\PhpCompiler\Tests\Node\Factory;

use Jaunas\PhpCompiler\Node\Expr\BinaryOp;
use Jaunas\PhpCompiler\Node\Expr\Number;
use Jaunas\PhpCompiler\Node\Expr\PhpNumber;
use Jaunas\PhpCompiler\Node\Factory\PrintFactory;
use Jaunas\PhpCompiler\Node\Expr\String_;
use Jaunas\PhpCompiler\Node\MacroCall;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PrintFactory::class)]
#[UsesClass(BinaryOp::class)]
#[UsesClass(Number::class)]
#[UsesClass(PhpNumber::class)]
#[UsesClass(String_::class)]
#[UsesClass(MacroCall::class)]
class PrintFactoryTest extends TestCase
{
    #[Test]
    public function createPrintWithString(): void
    {
        $expected = new MacroCall('print', new String_('test string'));
        $print = PrintFactory::createWithString('test string');

        $this->assertEquals($expected, $print);
    }

    #[Test]
    public function createPrintWithNumber(): void
    {
        $expected = new MacroCall('print', new String_('{}'), new PhpNumber(new Number(5)));
        $print = PrintFactory::createWithNumber(5);

        $this->assertEquals($expected, $print);
    }

    #[Test]
    public function createPrintWithBinaryOp(): void
    {
        $binaryOp = new BinaryOp('+', new Number(5), new Number(3));
        $expected = new MacroCall(
            'print',
            new String_('{}'),
            new PhpNumber($binaryOp)
        );
        $print = PrintFactory::createWithNumber($binaryOp);

        $this->assertEquals($expected, $print);
    }

    #[Test]
    public function createPrintWithStringExpr(): void
    {
        $stringExpr = new String_('test string');
        $expected = new MacroCall('print', $stringExpr);

        $print = PrintFactory::createWithExpr($stringExpr);
        $this->assertEquals($expected, $print);
    }

    #[Test]
    public function createPrintWithNumberExpr(): void
    {
        $numberExpr =  new Number(5);
        $expected = new MacroCall('print', new String_('{}'), new PhpNumber($numberExpr));

        $print = PrintFactory::createWithExpr($numberExpr);
        $this->assertEquals($expected, $print);
    }
}
