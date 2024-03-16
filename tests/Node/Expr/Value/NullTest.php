<?php

namespace Jaunas\PhpCompiler\Tests\Node\Expr\Value;

use Jaunas\PhpCompiler\Node\Expr\Value\Null_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Null_::class)]
class NullTest extends TestCase
{
    #[Test]
    public function source(): void
    {
        $null = new Null_();
        $this->assertEquals('Value::Null', $null->getSource());
    }
}
