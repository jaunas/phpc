<?php

namespace Jaunas\PhpCompiler\Tests\Node\Expr;

use Jaunas\PhpCompiler\Node\Expr\FnCall;
use Jaunas\PhpCompiler\Node\Expr\StrRef;
use Jaunas\PhpCompiler\Node\Expr\Value\Bool_;
use Jaunas\PhpCompiler\Node\Expr\Value\String_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FnCall::class)]
#[UsesClass(Bool_::class)]
#[UsesClass(StrRef::class)]
#[UsesClass(String_::class)]
class FnCallTest extends TestCase
{
    #[Test]
    public function getSourceNoArguments(): void
    {
        $fnCall = new FnCall('to_bool');
        $fnCall->setSubject(new Bool_(true));

        $this->assertEquals('Value::Bool(true).to_bool()', $fnCall->getSource());
    }

    #[Test]
    public function getSourceWithArgument(): void
    {
        $fnCall = new FnCall('concat', [String_::fromString('text 2')]);
        $fnCall->setSubject(String_::fromString('text 1, '));

        $this->assertEquals(
            'Value::String("text 1, ".to_string()).concat(Value::String("text 2".to_string()))',
            $fnCall->getSource()
        );
    }
}
