<?php

namespace Jaunas\PhpCompiler\Visitor;

use Jaunas\PhpCompiler\Node\Factory\FunctionCallFactory;
use Jaunas\PhpCompiler\Node\Fn_;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall as PhpFuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Echo_ as PhpEcho;
use PhpParser\NodeVisitorAbstract;

class Echo_ extends NodeVisitorAbstract
{
    public function __construct(private readonly Fn_ $fn)
    {
    }

    public function enterNode(Node $node): null
    {
        if ($node instanceof PhpEcho) {
            $args = [];
            foreach ($node->exprs as $phpExpr) {
                $args[] = new Arg($phpExpr);
            }

            $funcCall = new PhpFuncCall(new Name('echo'), $args);
            $this->fn->addStatement(FunctionCallFactory::fromFuncCall($funcCall));
        }

        return null;
    }
}
