<?php

namespace Jaunas\PhpCompiler\Tests\Node\Expr\Value;

use Jaunas\PhpCompiler\Node\Expr\StrRef;
use Jaunas\PhpCompiler\Node\Expr\Value\String_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(String_::class)]
class StringTest extends TestCase
{
    #[Test]
    #[DataProvider('contentProvider')]
    public function source(string $expected, StrRef $content): void
    {
        $string = new String_($content);
        $this->assertEquals($expected, $string->getSource());
    }

    /**
     * @return array<string, array{expected: string, content: StrRef}>
     */
    public static function contentProvider(): array
    {
        return [
            'empty' => [
                'expected' => 'rust_php::Value::String("".to_string())',
                'content' => new StrRef(''),
            ],
            'withContent' => [
                'expected' => 'rust_php::Value::String("string content".to_string())',
                'content' => new StrRef('string content'),
            ],
        ];
    }

    #[Test]
    public function canCreateFromString(): void
    {
        $expected = new String_(new StrRef('string content'));
        $value = String_::fromString('string content');

        $this->assertEquals($expected, $value);
    }
}
