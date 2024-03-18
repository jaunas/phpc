<?php

namespace Jaunas\PhpCompiler\Tests\Node\Factory;

use Jaunas\PhpCompiler\Exception\FunctionNotFound;
use Jaunas\PhpCompiler\Node\Expr\FunctionCall;
use Jaunas\PhpCompiler\Node\Factory\FunctionCallFactory;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Float_;
use PhpParser\Node\Scalar\String_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FunctionCallFactory::class)]
class FunctionCallFactoryTest extends TestCase
{
    #[Test]
    #[DataProvider('funcCallProvider')]
    public function canCreateFromFuncCall(string $expected, FuncCall $funcCall): void
    {
        $functionCall = FunctionCallFactory::fromFuncCall($funcCall);

        $this->assertInstanceOf(FunctionCall::class, $functionCall);
        $this->assertEquals($expected, $functionCall->getSource());
    }

    /**
     * @return array<array{expected: string, funcCall: FuncCall}>
     */
    public static function funcCallProvider(): array
    {
        return [
            [
                'expected' => 'functions::VarDump::call(vec![Value::Null]).unwrap()',
                'funcCall' => new FuncCall(new Name('var_dump'), [new Arg(new ConstFetch(new Name('null')))]),
            ],
            [
                'expected' => 'functions::FileExists::call(vec![Value::String("test.txt".to_string())]).unwrap()',
                'funcCall' => new FuncCall(new Name('file_exists'), [new Arg(new String_('test.txt'))]),
            ],
            [
                'expected' => 'functions::VarDump::call(vec![Value::Number(3.14_f64)]).unwrap()',
                'funcCall' => new FuncCall(new Name('var_dump'), [new Arg(new Float_(3.14))]),
            ],
        ];
    }

    #[Test]
    public function cannotCreateFromNotRegisteredFunction(): void
    {
        $funCall = new FuncCall(new Name('foo_bar'), []);

        $this->expectException(FunctionNotFound::class);
        FunctionCallFactory::fromFuncCall($funCall);
    }
}
