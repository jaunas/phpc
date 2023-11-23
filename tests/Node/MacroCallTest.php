<?php

namespace Jaunas\PhpCompiler\Tests\Node;

use Jaunas\PhpCompiler\Node\MacroCall;
use Jaunas\PhpCompiler\Node\String_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(MacroCall::class)]
class MacroCallTest extends TestCase
{
    #[Test]
    #[DataProvider('nameProvider')]
    public function canPrint(string $expected, string $name): void
    {
        $macroCall = new MacroCall($name);
        $this->assertEquals($expected, $macroCall->print());
    }

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
        $macroCall = new MacroCall($name, new String_($argument));
        $this->assertEquals($expected, $macroCall->print());
    }

    public static function nameAndArgumentProvider(): array
    {
        $data = self::nameProvider();

        $data['println']['expected'] = "println!(\"Hello, world!\");\n";
        $data['println']['argument'] = 'Hello, world!';

        $data['format']['expected'] = "format!(\"Example string\");\n";
        $data['format']['argument'] = 'Example string';

        return $data;
    }
}
