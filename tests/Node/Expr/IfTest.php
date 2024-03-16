<?php

namespace Jaunas\PhpCompiler\Tests\Node\Expr;

use Jaunas\PhpCompiler\Node\Expr\FnCall;
use Jaunas\PhpCompiler\Node\Expr\Value\Bool_;
use Jaunas\PhpCompiler\Node\Expr\If_;
use Jaunas\PhpCompiler\Node\Expr\Value\Number;
use Jaunas\PhpCompiler\Node\Expr\StrRef;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(If_::class)]
#[UsesClass(Bool_::class)]
#[UsesClass(Number::class)]
#[UsesClass(StrRef::class)]
#[UsesClass(FnCall::class)]
class IfTest extends TestCase
{
    #[Test]
    #[DataProvider('stringProvider')]
    public function canPrintWithString(string $expected, bool $condition, string $then, string $else): void
    {
        $if = new If_(new Bool_($condition), new StrRef($then), new StrRef($else));
        $this->assertEquals($expected, $if->getSource());
    }

    /**
     * @return array<array{string, bool, string, string}>
     */
    public static function stringProvider(): array
    {
        return [
            ['if Value::Bool(true).to_bool() { "true" } else { "false" }', true, 'true', 'false'],
            ['if Value::Bool(false).to_bool() { "false" } else { "true" }', false, 'false', 'true'],
        ];
    }

    #[Test]
    public function canPrintWithNumber(): void
    {
        $if = new If_(new Bool_(true), new Number(5), new Number(8));
        $expected = 'if Value::Bool(true).to_bool() { Value::Number(5_f64) } else { Value::Number(8_f64) }';
        $this->assertEquals(
            $expected,
            $if->getSource()
        );
    }

    #[Test]
    public function canPrintWithExpr(): void
    {
        $if = new If_(new FnCall('condition_fn'), new FnCall('then_fn'), new FnCall('else_fn'));
        $this->assertEquals('if condition_fn() { then_fn() } else { else_fn() }', $if->getSource());
    }
}
