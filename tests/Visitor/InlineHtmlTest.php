<?php

namespace Jaunas\PhpCompiler\Tests\Visitor;

use Jaunas\PhpCompiler\Node\Expr\String_ as RustString;
use Jaunas\PhpCompiler\Node\Fn_ as RustFn;
use Jaunas\PhpCompiler\Node\MacroCall as RustMacroCall;
use Jaunas\PhpCompiler\Visitor\InlineHtml as InlineHtmlVisitor;
use PhpParser\Node\Stmt\Function_ as PhpFunction;
use PhpParser\Node\Stmt\InlineHTML as PhpInlineHTML;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InlineHtmlVisitor::class)]
#[UsesClass(RustString::class)]
#[UsesClass(RustFn::class)]
#[UsesClass(RustMacroCall::class)]
class InlineHtmlTest extends TestCase
{
    #[Test]
    public function doesNothingWhenDontMatch(): void
    {
        $main = new RustFn('main');

        $visitor = new InlineHtmlVisitor($main);
        $visitor->enterNode(new PhpFunction('customFunction'));

        $this->assertEmpty($main->getBody());
    }

    #[Test]
    public function addsPrintToFn(): void
    {
        $main = new RustFn('main');

        $visitor = new InlineHtmlVisitor($main);
        $visitor->enterNode(new PhpInlineHTML('example text'));

        $this->assertCount(1, $main->getBody());
        $print = $main->getBody()[0];
        $this->assertEquals("print!(\"example text\");\n", $print->print());
    }
}
