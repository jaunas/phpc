<?php

namespace Jaunas\PhpCompiler\Visitor;

use Jaunas\PhpCompiler\Node\Expr\Number as RustNumber;
use Jaunas\PhpCompiler\Node\Expr\BinaryOp as RustBinaryOp;
use Jaunas\PhpCompiler\Node\Factory\PrintFactory;
use Jaunas\PhpCompiler\Node\Fn_ as RustFn;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp as PhpBinaryOp;
use PhpParser\Node\Expr\BinaryOp\Concat as PhpConcat;
use PhpParser\Node\Scalar\LNumber as PhpLNumber;
use PhpParser\Node\Scalar\String_ as PhpString;
use PhpParser\Node\Stmt\Echo_ as PhpEcho;
use PhpParser\NodeVisitorAbstract;

class Echo_ extends NodeVisitorAbstract
{
    public function __construct(private readonly RustFn $fn)
    {
    }

    public function enterNode(Node $node): null
    {
        if ($node instanceof PhpEcho) {
            foreach ($node->exprs as $expr) {
                $this->enterExpr($expr);
            }
        }

        return null;
    }

    private function enterExpr(Node $node): void
    {
        if ($node instanceof PhpString) {
            $this->fn->addToBody(PrintFactory::createWithString($node->value));
        } elseif ($node instanceof PhpLNumber) {
            $this->fn->addToBody(PrintFactory::createWithNumber($node->value));
        } elseif ($node instanceof PhpConcat) {
            $this->enterExpr($node->left);
            $this->enterExpr($node->right);
        } elseif ($node instanceof PhpBinaryOp) {
            if ($node->left instanceof PhpLNumber && $node->right instanceof PhpLNumber) {
                $this->fn->addToBody(PrintFactory::createWithNumber(new RustBinaryOp(
                    $node->getOperatorSigil(),
                    new RustNumber($node->left->value),
                    new RustNumber($node->right->value)
                )));
            }
        }
    }
}
