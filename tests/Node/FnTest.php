<?php

namespace Jaunas\PhpCompiler\Tests\Node;

use Jaunas\PhpCompiler\Node\Fn_;
use Jaunas\PhpCompiler\Node\MacroCall as RustMacroCall;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Fn_::class)]
#[UsesClass(RustMacroCall::class)]
class FnTest extends TestCase
{
    /**
     * @return array<string, array<string, string>>
     */
    public static function fnProvider(): array
    {
        return [
            'main' => [
                'name' => 'main',
                'print' => "fn main() {\n}\n",
            ],
            'custom_function' => [
                'name' => 'custom_function',
                'print' => "fn custom_function() {\n}\n",
            ],
        ];
    }

    #[Test]
    #[DataProvider('fnProvider')]
    public function getName(string $name): void
    {
        $fn = new Fn_($name);
        $this->assertEquals($fn->getName(), $name);
    }

    #[Test]
    public function emptyFnGetBody(): void
    {
        $fn = new Fn_('main');
        $this->assertEmpty($fn->getBody());
    }

    #[Test]
    public function fnWithStatementGetBody(): void
    {
        $fn = new Fn_('main');
        $println = new RustMacroCall('println');
        $fn->addToBody($println);
        $this->assertCount(1, $fn->getBody());

        $this->assertEquals($println, $fn->getBody()[0]);
    }

    #[Test]
    #[DataProvider('fnProvider')]
    public function emptyFnPrint(string $name, string $expectedPrint): void
    {
        $fn = new Fn_($name);
        $this->assertEquals($expectedPrint, $fn->print());
    }

    #[Test]
    public function fnWithStatementPrint(): void
    {
        $fn = new Fn_('main');
        $println = new RustMacroCall('println');
        $fn->addToBody($println);

        $this->assertEquals("fn main() {\nprintln!();\n}\n", $fn->print());
    }
}
