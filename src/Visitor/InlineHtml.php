<?php

namespace Jaunas\PhpCompiler\Visitor;

use Jaunas\PhpCompiler\Node\Expr\String_;
use Jaunas\PhpCompiler\Node\Fn_;
use Jaunas\PhpCompiler\Node\MacroCall;
use PhpParser\Node;
use PhpParser\Node\Stmt\InlineHTML as InlineHTMLNode;
use PhpParser\NodeVisitorAbstract;

class InlineHtml extends NodeVisitorAbstract
{
    public function __construct(private readonly Fn_ $main)
    {
    }

    public function enterNode(Node $node): null
    {
        if ($node instanceof InlineHTMLNode) {
            $this->main->addToBody(new MacroCall('print', new String_($node->value)));
        }

        return null;
    }
}
