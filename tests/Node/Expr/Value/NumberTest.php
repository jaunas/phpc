<?php

namespace Jaunas\PhpCompiler\Tests\Node\Expr\Value;

use Jaunas\PhpCompiler\Node\Expr\Value\Number;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Number::class)]
class NumberTest extends TestCase
{
    #[Test]
    public function getSource(): void
    {
        $number = new Number(3.14);
        $this->assertEquals('rust_php::Value::Number(3.14_f64)', $number->getSource());
    }
}
