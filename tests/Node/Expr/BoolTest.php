<?php

namespace Jaunas\PhpCompiler\Tests\Node\Expr;

use Jaunas\PhpCompiler\Node\Expr\Bool_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Bool_::class)]
class BoolTest extends TestCase
{
    #[Test]
    #[DataProvider('boolProvider')]
    public function canPrint(string $expected, bool $value): void
    {
        $bool = new Bool_($value);
        $this->assertEquals($expected, $bool->getSource());
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function boolProvider(): array
    {
        return [
            'true' => ['true', true],
            'false' => ['false', false],
        ];
    }
}
