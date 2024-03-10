<?php

namespace Jaunas\PhpCompiler\Tests\Node\Expr;

use Jaunas\PhpCompiler\Node\Expr\Value\Number;
use Jaunas\PhpCompiler\Node\Expr\BinaryOp;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BinaryOp::class)]
#[UsesClass(Number::class)]
class BinaryOpTest extends TestCase
{
    #[Test]
    #[DataProvider('numberPairsProvider')]
    public function canPrint(string $expected, string $sign, int $left, int $right): void
    {
        $plus = new BinaryOp($sign, new Number($left), new Number($right));
        $this->assertEquals($expected, $plus->getSource());
    }

    /**
     * @return array<string, array{string, string, int, int}>
     */
    public static function numberPairsProvider(): array
    {
        return [
            '5 + 3' => ['rust_php::Value::Number(5_f64) + rust_php::Value::Number(3_f64)', '+', 5, 3],
            '3 + 14' => ['rust_php::Value::Number(3_f64) + rust_php::Value::Number(14_f64)', '+', 3, 14],
            '5 - 3' => ['rust_php::Value::Number(5_f64) - rust_php::Value::Number(3_f64)', '-', 5, 3],
            '5 * 3' => ['rust_php::Value::Number(5_f64) * rust_php::Value::Number(3_f64)', '*', 5, 3],
        ];
    }

    #[Test]
    public function canHaveNestedBinaryOp(): void
    {
        $left = new BinaryOp('+', new Number(3), new Number(4));
        $right = new Number(5);

        $nested = new BinaryOp('+', $left, $right);
        $this->assertEquals(
            '(rust_php::Value::Number(3_f64) + rust_php::Value::Number(4_f64)) + rust_php::Value::Number(5_f64)',
            $nested->getSource()
        );
    }
}
