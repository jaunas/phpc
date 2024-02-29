<?php

namespace Jaunas\PhpCompiler\Tests\Node\Factory;

use Jaunas\PhpCompiler\Node\Expr\BinaryOp;
use Jaunas\PhpCompiler\Node\Expr\Number;
use Jaunas\PhpCompiler\Node\Expr\PhpNumber;
use Jaunas\PhpCompiler\Node\Expr\StrRef;
use Jaunas\PhpCompiler\Node\Expr\Value\Null_;
use Jaunas\PhpCompiler\Node\Expr\Value\String_;
use Jaunas\PhpCompiler\Node\Factory\PrintFactory;
use Jaunas\PhpCompiler\Node\MacroCall;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PrintFactory::class)]
#[UsesClass(BinaryOp::class)]
#[UsesClass(Number::class)]
#[UsesClass(PhpNumber::class)]
#[UsesClass(StrRef::class)]
#[UsesClass(MacroCall::class)]
class PrintFactoryTest extends TestCase
{
    #[Test]
    public function createPrintWithNull(): void
    {
        $expected = new MacroCall('print', StrRef::placeholder(), new Null_());
        $print = PrintFactory::createWithNull();

        $this->assertEquals($expected, $print);
    }

    #[Test]
    public function createPrintWithStringValue(): void
    {
        $expected = new MacroCall('print', StrRef::placeholder(), String_::fromString('test string'));
        $print = PrintFactory::createWithString('test string');

        $this->assertEquals($expected, $print);
    }

    #[Test]
    public function createPrintWithNumber(): void
    {
        $expected = new MacroCall('print', StrRef::placeholder(), new PhpNumber(new Number(5)));
        $print = PrintFactory::createWithNumber(5);

        $this->assertEquals($expected, $print);
    }

    #[Test]
    public function createPrintWithBinaryOp(): void
    {
        $binaryOp = new BinaryOp('+', new Number(5), new Number(3));
        $expected = new MacroCall('print', StrRef::placeholder(), new PhpNumber($binaryOp));
        $print = PrintFactory::createWithNumber($binaryOp);

        $this->assertEquals($expected, $print);
    }

    #[Test]
    public function createPrintWithStringExpr(): void
    {
        $stringExpr = new StrRef('test string');
        $expected = new MacroCall('print', $stringExpr);

        $print = PrintFactory::createWithExpr($stringExpr);
        $this->assertEquals($expected, $print);
    }

    #[Test]
    public function createPrintWithNumberExpr(): void
    {
        $numberExpr = new Number(5);
        $expected = new MacroCall('print', StrRef::placeholder(), new PhpNumber($numberExpr));

        $print = PrintFactory::createWithExpr($numberExpr);
        $this->assertEquals($expected, $print);
    }
}
