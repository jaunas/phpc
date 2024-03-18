<?php

namespace Jaunas\PhpCompiler\Visitor;

use Jaunas\PhpCompiler\Exception\FunctionNotFound;
use Jaunas\PhpCompiler\Node\Factory\FunctionCallFactory;
use Jaunas\PhpCompiler\Node\Fn_;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall as PhpFuncCall;
use PhpParser\NodeVisitorAbstract;

class FuncCall extends NodeVisitorAbstract
{
    public function __construct(private readonly Fn_ $main)
    {
    }

    public function enterNode(Node $node): null
    {
        if ($node instanceof PhpFuncCall) {
            try {
                $this->main->addStatement(FunctionCallFactory::fromFuncCall($node));
            } catch (FunctionNotFound) {
                // Do nothing
            }
        }

        return null;
    }
}
