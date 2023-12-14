<?php

namespace Jaunas\PhpCompiler\Tests\Node\Expr;

use Jaunas\PhpCompiler\Node\Expr\Bool_;
use Jaunas\PhpCompiler\Node\Expr\If_;
use Jaunas\PhpCompiler\Node\Expr\Number;
use Jaunas\PhpCompiler\Node\Expr\String_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(If_::class)]
class IfTest extends TestCase
{
    #[Test]
    #[DataProvider('stringProvider')]
    public function canPrintWithString(string $expected, bool $condition, string $then, string $else): void
    {
        $if = new If_(new Bool_($condition), new String_($then), new String_($else));
        $this->assertEquals($expected, $if->getSource());
    }

    /**
     * @return array<array{string, bool, string, string}>
     */
    public static function stringProvider(): array
    {
        return [
            ['if true { "true" } else { "false" }', true, 'true', 'false'],
            ['if false { "false" } else { "true" }', false, 'false', 'true'],
        ];
    }

    #[Test]
    public function canPrintWithNumber(): void
    {
        $if = new If_(new Bool_(true), new Number(5), new Number(8));
        $this->assertEquals('if true { 5_f64 } else { 8_f64 }', $if->getSource());
    }
}
