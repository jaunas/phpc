<?php

namespace Jaunas\PhpCompiler\Visitor;

use Jaunas\PhpCompiler\Node\Expr\FunctionCall;
use Jaunas\PhpCompiler\Node\Expr\Value\String_;
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
            $this->main->addStatement(new FunctionCall('Echo', [String_::fromString($node->value)]));
        }

        return null;
    }
}
