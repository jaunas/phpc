<?php

namespace Jaunas\PhpCompiler\Visitor;

use Jaunas\PhpCompiler\Node\Expr\Number;
use Jaunas\PhpCompiler\Node\Expr\String_;
use Jaunas\PhpCompiler\Node\Fn_;
use Jaunas\PhpCompiler\Node\MacroCall;
use PhpParser\Node;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_ as StringNode;
use PhpParser\Node\Stmt\Echo_ as EchoNode;
use PhpParser\NodeVisitorAbstract;

class Echo_ extends NodeVisitorAbstract
{
    public function __construct(private readonly Fn_ $fn)
    {
    }

    public function enterNode(Node $node): void
    {
        if ($node instanceof EchoNode && isset($node->exprs[0])) {
            $node = $node->exprs[0];
            if ($node instanceof StringNode) {
                $this->fn->addToBody(new MacroCall('print', new String_($node->value)));
            } elseif ($node instanceof LNumber) {
                $this->fn->addToBody(new MacroCall('print', new String_('{}'), new Number($node->value)));
            }
        }
    }
}
