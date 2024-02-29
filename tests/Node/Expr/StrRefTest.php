<?php

namespace Jaunas\PhpCompiler\Tests\Node\Expr;

use Jaunas\PhpCompiler\Node\Expr\StrRef;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(StrRef::class)]
class StrRefTest extends TestCase
{
    #[Test]
    #[DataProvider('contentProvider')]
    public function canPrint(string $expectedContent, string $content): void
    {
        $string = new StrRef($content);
        $this->assertEquals($expectedContent, $string->getSource());
    }

    /**
     * @return array<string, string[]>
     */
    public static function contentProvider(): array
    {
        return [
            'empty' => ['""', ''],
            'example' => ['"Example text"', 'Example text'],
        ];
    }

    #[Test]
    public function canCreatePlaceholder(): void
    {
        $expected = new StrRef('{}');
        $string = StrRef::placeholder();

        $this->assertEquals($expected, $string);
    }
}
