<?php

namespace Jaunas\PhpCompiler\Tests\Node\Expr;

use Jaunas\PhpCompiler\Node\Expr\Number;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Number::class)]
class NumberTest extends TestCase
{
    #[Test]
    #[DataProvider('numberProvider')]
    public function canPrint(int $expected): void
    {
        $number = new Number($expected);
        $this->assertSame("$expected", $number->print());
    }

    /**
     * @return int[][]
     */
    public static function numberProvider(): array
    {
        return [[5], [314159]];
    }
}
