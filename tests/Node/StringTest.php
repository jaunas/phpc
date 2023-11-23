<?php

namespace Jaunas\PhpCompiler\Tests\Node;

use Jaunas\PhpCompiler\Node\Expr\String_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(String_::class)]
class StringTest extends TestCase
{
    #[Test]
    #[DataProvider('contentProvider')]
    public function canPrint(string $expectedContent, string $content): void
    {
        $string = new String_($content);
        $this->assertEquals($expectedContent, $string->print());
    }

    public static function contentProvider(): array
    {
        return [
            'empty' => ['""', ''],
            'example' => ['"Example text"', 'Example text'],
        ];
    }
}
