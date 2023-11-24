<?php

namespace Jaunas\PhpCompiler\Tests\Visitor;

use Jaunas\PhpCompiler\Node\Fn_;
use Jaunas\PhpCompiler\Visitor\InlineHtml;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\InlineHTML as InlineHTMLNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(InlineHtml::class)]
class InlineHtmlTest extends TestCase
{
    #[Test]
    public function doesNothingWhenDontMatch(): void
    {
        $main = new Fn_('main');

        $visitor = new InlineHtml($main);
        $visitor->enterNode(new Function_('customFunction'));

        $this->assertEmpty($main->getBody());
    }

    #[Test]
    public function addsPrintToFn(): void
    {
        $main = new Fn_('main');

        $visitor = new InlineHtml($main);
        $visitor->enterNode(new InlineHTMLNode('example text'));

        $this->assertCount(1, $main->getBody());
        $print = $main->getBody()[0];
        $this->assertEquals("print!(\"example text\");\n", $print->print());
    }
}
