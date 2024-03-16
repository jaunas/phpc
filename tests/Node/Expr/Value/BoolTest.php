<?php

namespace Jaunas\PhpCompiler\Tests\Node\Expr\Value;

use Jaunas\PhpCompiler\Node\Expr\Value\Bool_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Bool_::class)]
class BoolTest extends TestCase
{
    #[Test]
    #[DataProvider('valueProvider')]
    public function source(string $expected, bool $value): void
    {
        $bool = new Bool_($value);
        $this->assertEquals($expected, $bool->getSource());
    }

    /**
     * @return array<string, array{expected: string, value: bool}>
     */
    public static function valueProvider(): array
    {
        return [
            'true' => [
                'expected' => 'Value::Bool(true)',
                'value' => true,
            ],
            'false' => [
                'expected' => 'Value::Bool(false)',
                'value' => false,
            ],
        ];
    }
}
