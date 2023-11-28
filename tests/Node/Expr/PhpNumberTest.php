<?php

namespace Jaunas\PhpCompiler\Tests\Node\Expr;

use Jaunas\PhpCompiler\Node\Expr\Number;
use Jaunas\PhpCompiler\Node\Expr\PhpNumber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PhpNumber::class)]
#[UsesClass(Number::class)]
class PhpNumberTest extends TestCase
{
    #[Test]
    public function canPrint(): void
    {
        $phpNumber = new PhpNumber(new Number(5));
        $this->assertEquals('rust_php::PhpNumber::new(5_f64)', $phpNumber->print());
    }
}
