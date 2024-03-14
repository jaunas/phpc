<?php

namespace Jaunas\PhpCompiler\Visitor;

use Jaunas\PhpCompiler\Node\Expr\MacroCall;
use Jaunas\PhpCompiler\Node\Expr\StrRef;
use Jaunas\PhpCompiler\Node\Fn_;
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
            $this->main->addStatement(new MacroCall('print', new StrRef($node->value)));
        }

        return null;
    }
}
