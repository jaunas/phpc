<?php

namespace Jaunas\PhpCompiler\Visitor;

use Jaunas\PhpCompiler\Node\Expr\ArithmeticExpr;
use Jaunas\PhpCompiler\Node\Expr\Number as RustNumber;
use Jaunas\PhpCompiler\Node\Expr\BinaryOp as RustBinaryOp;
use Jaunas\PhpCompiler\Node\Factory\PrintFactory;
use Jaunas\PhpCompiler\Node\Fn_ as RustFn;
use PhpParser\Node;
use PhpParser\Node\Expr as PhpExpr;
use PhpParser\Node\Expr\BinaryOp as PhpBinaryOp;
use PhpParser\Node\Expr\BinaryOp\Concat as PhpConcat;
use PhpParser\Node\Scalar\Int_ as PhpInt;
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
        } elseif ($node instanceof PhpConcat) {
            $this->enterExpr($node->left);
            $this->enterExpr($node->right);
        } elseif ($node instanceof PhpInt || $node instanceof PhpBinaryOp) {
            $arithmeticExpr = $this->getArithmeticExpr($node);
            if ($arithmeticExpr instanceof ArithmeticExpr) {
                $this->fn->addToBody(PrintFactory::createWithNumber($arithmeticExpr));
            }
        }
    }

    private function getArithmeticExpr(PhpExpr $node): ?ArithmeticExpr
    {
        if ($node instanceof PhpInt) {
            return new RustNumber($node->value);
        }

        if ($node instanceof PhpBinaryOp) {
            $left = $this->getArithmeticExpr($node->left);
            $right = $this->getArithmeticExpr($node->right);
            if ($left instanceof ArithmeticExpr && $right instanceof ArithmeticExpr) {
                return new RustBinaryOp($node->getOperatorSigil(), $left, $right);
            }
        }

        return null;
    }
}
