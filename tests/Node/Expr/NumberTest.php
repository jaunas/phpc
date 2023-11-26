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
    public function canPrint(string $expected, int $value): void
    {
        $number = new Number($value);
        $this->assertEquals($expected, $number->print());
    }

    /**
     * @return array{expected: string, value: int}[]
     */
    public static function numberProvider(): array
    {
        return [
            '5' => [
                'expected' => '5_f64',
                'value' => 5,
            ],
            '314159' => [
                'expected' => '314159_f64',
                'value' => 314159,
            ]
        ];
    }
}
