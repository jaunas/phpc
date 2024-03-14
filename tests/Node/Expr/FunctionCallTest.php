<?php

namespace Jaunas\PhpCompiler\Tests\Node\Expr;

use Jaunas\PhpCompiler\Node\Expr\FunctionCall;
use Jaunas\PhpCompiler\Node\Expr\Value\Bool_;
use Jaunas\PhpCompiler\Node\Expr\Value\Number;
use Jaunas\PhpCompiler\Node\Expr\Value\String_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FunctionCall::class)]
class FunctionCallTest extends TestCase
{
    #[Test]
    #[DataProvider('sourceProvider')]
    public function source(string $expected, string $functionName, array $args): void
    {
        $functionCall = new FunctionCall($functionName, $args);
        $this->assertEquals($expected, $functionCall->getSource());
    }

    public static function sourceProvider(): array
    {
        return [
            [
                'expected' => 'rust_php::functions::Echo::call(vec![' .
                    'rust_php::Value::String("text and number: ".to_string()), ' .
                    'rust_php::Value::Number(3.14_f64)' .
                    '])',
                'functionName' => 'Echo',
                'args' => [
                    String_::fromString('text and number: '),
                    new Number(3.14),
                ],
            ],
            [
                'expected' => 'rust_php::functions::VarDump::call(vec![rust_php::Value::Bool(true)])',
                'functionName' => 'VarDump',
                'args' => [new Bool_(true)],
            ]
        ];
    }
}
