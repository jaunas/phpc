<?php

namespace Jaunas\PhpCompiler\Tests\Node;

use Jaunas\PhpCompiler\Node\Fn_;
use Jaunas\PhpCompiler\Node\Mod;
use Jaunas\PhpCompiler\Node\Node;
use Jaunas\PhpCompiler\Node\Use_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Mod::class)]
class ModTest extends TestCase
{
    /**
     * @param Node[] $statements
     */
    #[Test]
    #[DataProvider('sourceProvider')]
    public function sourceWithFn(string $expected, array $statements): void
    {
        $mod = new Mod();
        foreach ($statements as $statement) {
            $mod->addStatement($statement);
        }

        $this->assertEquals($expected, $mod->getSource());
    }

    /**
     * @return array<array{expected: string, statements: Node[]}>
     */
    public static function sourceProvider(): array
    {
        return [
            [
                'expected' => "",
                'statements' => [],
            ],
            [
                'expected' => "fn test_fn() {\n}\n",
                'statements' => [new Fn_('test_fn')],
            ],
            [
                'expected' => "fn first_fn() {\n}\nfn second_fn() {\n}\n",
                'statements' => [
                    new Fn_('first_fn'),
                    new Fn_('second_fn'),
                ],
            ],
            [
                'expected' => "use rust_php::*;\nuse rust_php::functions::Function;\nfn main() {\n}\n",
                'statements' => [
                    new Use_(['rust_php', '*']),
                    new Use_(['rust_php', 'functions', 'Function']),
                    new Fn_('main'),
                ],
            ],
        ];
    }
}
