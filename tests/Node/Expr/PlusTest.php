<?php

namespace Jaunas\PhpCompiler\Tests\Node\Expr;

use Jaunas\PhpCompiler\Node\Expr\Number;
use Jaunas\PhpCompiler\Node\Expr\Plus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Plus::class)]
class PlusTest extends TestCase
{
    #[Test]
    #[DataProvider('numberPairsProvider')]
    public function canPrint(string $expected, int $left, int $right): void
    {
        $plus = new Plus(new Number($left), new Number($right));
        $this->assertEquals($expected, $plus->print());
    }

    /**
     * @return array<string, array{string, int, int}>
     */
    public static function numberPairsProvider(): array
    {
        return [
            '5 + 3' => ['5 + 3', 5, 3],
            '3 + 14' => ['3 + 14', 3, 14],
        ];
    }
}
