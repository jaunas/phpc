<?php

namespace Jaunas\PhpCompiler\Tests\Node;

use Jaunas\PhpCompiler\Node\Expr\Number as RustNumber;
use Jaunas\PhpCompiler\Node\Expr\PhpNumber;
use Jaunas\PhpCompiler\Node\Expr\String_ as RustString;
use Jaunas\PhpCompiler\Node\MacroCall;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MacroCall::class)]
#[UsesClass(RustNumber::class)]
#[UsesClass(RustString::class)]
class MacroCallTest extends TestCase
{
    #[Test]
    #[DataProvider('nameProvider')]
    public function canPrint(string $expected, string $name): void
    {
        $macroCall = new MacroCall($name);
        $this->assertEquals($expected, $macroCall->print());
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function nameProvider(): array
    {
        return [
            'println' => [
                'expected' => "println!();\n",
                'name' => 'println',
            ],
            'format' => [
                'expected' => "format!();\n",
                'name' => 'format',
            ],
        ];
    }

    #[Test]
    #[DataProvider('nameAndArgumentProvider')]
    public function canPrintWithAnArgument(string $expected, string $name, string $argument): void
    {
        $macroCall = new MacroCall($name, new RustString($argument));
        $this->assertEquals($expected, $macroCall->print());
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function nameAndArgumentProvider(): array
    {
        $data = self::nameProvider();

        $data['println']['expected'] = "println!(\"Hello, world!\");\n";
        $data['println']['argument'] = 'Hello, world!';

        $data['format']['expected'] = "format!(\"Example string\");\n";
        $data['format']['argument'] = 'Example string';

        return $data;
    }

    #[Test]
    public function canPassTwoArguments(): void
    {
        $macroCall = new MacroCall('print', new RustString('{}'), new PhpNumber(new RustNumber(5)));
        $this->assertEquals("print!(\"{}\", rust_php::PhpNumber::new(5_f64));\n", $macroCall->print());
    }
}
