<?php

namespace Jaunas\PhpCompiler\Tests\Node\Expr;

use Jaunas\PhpCompiler\Node\Expr\MacroCall;
use Jaunas\PhpCompiler\Node\Expr\StrRef;
use Jaunas\PhpCompiler\Node\Expr\Value\Null_;
use Jaunas\PhpCompiler\Node\Expr\Value\Number;
use Jaunas\PhpCompiler\Node\Expr\Value\String_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MacroCall::class)]
#[UsesClass(Number::class)]
#[UsesClass(StrRef::class)]
class MacroCallTest extends TestCase
{
    #[Test]
    #[DataProvider('nameProvider')]
    public function canPrint(string $expected, string $name): void
    {
        $macroCall = new MacroCall($name);
        $this->assertEquals($expected, $macroCall->getSource());
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function nameProvider(): array
    {
        return [
            'println' => [
                'expected' => "println!()",
                'name' => 'println',
            ],
            'format' => [
                'expected' => "format!()",
                'name' => 'format',
            ],
        ];
    }

    #[Test]
    #[DataProvider('nameAndArgumentProvider')]
    public function canPrintWithAnArgument(string $expected, string $name, string $argument): void
    {
        $macroCall = new MacroCall($name, new StrRef($argument));
        $this->assertEquals($expected, $macroCall->getSource());
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function nameAndArgumentProvider(): array
    {
        $data = self::nameProvider();

        $data['println']['expected'] = "println!(\"Hello, world!\")";
        $data['println']['argument'] = 'Hello, world!';

        $data['format']['expected'] = "format!(\"Example string\")";
        $data['format']['argument'] = 'Example string';

        return $data;
    }

    #[Test]
    public function canPassTwoArguments(): void
    {
        $macroCall = new MacroCall('print', StrRef::placeholder(), new Number(5));
        $this->assertEquals("print!(\"{}\", rust_php::Value::Number(5_f64))", $macroCall->getSource());
    }

    #[Test]
    public function canPassMoreArguments(): void
    {
        $string = String_::fromString('null value: ');
        $null = new Null_();

        $macroCall = new MacroCall('print', new StrRef('{}{}'), $string, $null);

        $expected = "print!(\"{}{}\", rust_php::Value::String(\"null value: \".to_string()), rust_php::Value::Null)";
        $this->assertEquals($expected, $macroCall->getSource());
    }
}
