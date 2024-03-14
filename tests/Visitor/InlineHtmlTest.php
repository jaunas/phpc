<?php

namespace Jaunas\PhpCompiler\Tests\Visitor;

use Jaunas\PhpCompiler\Node\Expr\MacroCall;
use Jaunas\PhpCompiler\Node\Expr\StrRef;
use Jaunas\PhpCompiler\Node\Fn_;
use Jaunas\PhpCompiler\Visitor\InlineHtml as InlineHtmlVisitor;
use PhpParser\Node\Stmt\Function_ as PhpFunction;
use PhpParser\Node\Stmt\InlineHTML as PhpInlineHTML;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InlineHtmlVisitor::class)]
#[UsesClass(StrRef::class)]
#[UsesClass(Fn_::class)]
#[UsesClass(MacroCall::class)]
class InlineHtmlTest extends TestCase
{
    #[Test]
    public function doesNothingWhenDontMatch(): void
    {
        $main = new Fn_('main');

        $visitor = new InlineHtmlVisitor($main);
        $visitor->enterNode(new PhpFunction('customFunction'));

        $this->assertEmpty($main->getBody());
    }

    #[Test]
    public function addsPrintToFn(): void
    {
        $main = new Fn_('main');

        $visitor = new InlineHtmlVisitor($main);
        $visitor->enterNode(new PhpInlineHTML('example text'));

        $this->assertCount(1, $main->getBody());
        $print = $main->getBody()[0];
        $this->assertEquals("print!(\"example text\")", $print->getSource());
    }
}
