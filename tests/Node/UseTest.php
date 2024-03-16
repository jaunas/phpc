<?php

namespace Jaunas\PhpCompiler\Tests\Node;

use Jaunas\PhpCompiler\Node\Use_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Use_::class)]
class UseTest extends TestCase
{
    #[Test]
    public function source(): void
    {
        $use = new Use_(['crate', 'submodule']);
        $this->assertEquals("use crate::submodule;\n", $use->getSource());
    }
}
