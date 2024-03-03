<?php

namespace Jaunas\PhpCompiler\Tests\Node\Expr\Value;

use Jaunas\PhpCompiler\Node\Expr\Value\Concat;
use Jaunas\PhpCompiler\Node\Expr\Value\Null_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Concat::class)]
class ConcatTest extends TestCase
{
    #[Test]
    public function getSourceForTwoNulls(): void
    {
        $concat = new Concat(new Null_(), new Null_());
        $this->assertEquals('rust_php::Value::Null.concat(rust_php::Value::Null)', $concat->getSource());
    }
}
