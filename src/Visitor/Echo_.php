<?php

namespace Jaunas\PhpCompiler\Visitor;

use Jaunas\PhpCompiler\Node\Expr\ArithmeticExpr;
use Jaunas\PhpCompiler\Node\Expr\Bool_ as RustBool;
use Jaunas\PhpCompiler\Node\Expr\Expr;
use Jaunas\PhpCompiler\Node\Expr\If_ as RustIf;
use Jaunas\PhpCompiler\Node\Expr\Number as RustNumber;
use Jaunas\PhpCompiler\Node\Expr\BinaryOp as RustBinaryOp;
use Jaunas\PhpCompiler\Node\Expr\String_ as RustString;
use Jaunas\PhpCompiler\Node\Factory\PrintFactory;
use Jaunas\PhpCompiler\Node\Fn_ as RustFn;
use Jaunas\PhpCompiler\Node\MacroCall as RustMacroCall;
use PhpParser\Node;
use PhpParser\Node\Expr as PhpExpr;
use PhpParser\Node\Expr\BinaryOp as PhpBinaryOp;
use PhpParser\Node\Expr\BinaryOp\Concat as PhpConcat;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Ternary as PhpTernary;
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
                foreach ($this->getMacroCalls($expr) as $macroCall) {
                    $this->fn->addToBody($macroCall);
                }
            }
        }

        return null;
    }

    /**
     * @return RustMacroCall[]
     */
    private function getMacroCalls(Node $node): array
    {
        if ($node instanceof PhpConcat) {
            return [...$this->getMacroCalls($node->left), ...$this->getMacroCalls($node->right)];
        } elseif ($node instanceof PhpTernary && $node->cond instanceof ConstFetch) {
            $condition = $this->getBoolExpr($node->cond);
            $then = $this->getExpr($node->if);
            $else = $this->getExpr($node->else);
            if ($condition instanceof RustBool && $then instanceof Expr && $else instanceof Expr) {
                return [new RustMacroCall(
                    'print',
                    new RustString('{}'),
                    new RustIf($condition, $then, $else)
                )];
            }
        } elseif (
            $node instanceof PhpString ||
            $node instanceof PhpInt ||
            $node instanceof PhpBinaryOp
        ) {
            $expr = $this->getExpr($node);
            if ($expr instanceof Expr) {
                return [PrintFactory::createWithExpr($expr)];
            }
        }

        return [];
    }

    private function getExpr(?PhpExpr $node): ?Expr
    {
        if ($node instanceof PhpString) {
            return new RustString($node->value);
        } elseif ($node instanceof PhpInt || $node instanceof PhpBinaryOp) {
            return $this->getArithmeticExpr($node);
        }

        return null;
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

    public function getBoolExpr(ConstFetch $node): ?RustBool
    {
        $name = $node->name->name;
        if ($name == 'true') {
            $condition = new RustBool(true);
        } elseif ($name == 'false') {
            $condition = new RustBool(false);
        }

        return $condition ?? null;
    }
}
