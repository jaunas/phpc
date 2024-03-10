<?php

namespace Jaunas\PhpCompiler\Visitor;

use Jaunas\PhpCompiler\Node\Expr\Expr;
use Jaunas\PhpCompiler\Node\Expr\If_;
use Jaunas\PhpCompiler\Node\Expr\BinaryOp;
use Jaunas\PhpCompiler\Node\Expr\StrRef;
use Jaunas\PhpCompiler\Node\Expr\Value\Bool_;
use Jaunas\PhpCompiler\Node\Expr\Value\Number;
use Jaunas\PhpCompiler\Node\Factory\PrintFactory;
use Jaunas\PhpCompiler\Node\Fn_;
use Jaunas\PhpCompiler\Node\MacroCall;
use PhpParser\Node;
use PhpParser\Node\Expr as PhpExpr;
use PhpParser\Node\Expr\BinaryOp as PhpBinaryOp;
use PhpParser\Node\Expr\BinaryOp\Concat as PhpConcat;
use PhpParser\Node\Expr\ConstFetch as PhpConstFetch;
use PhpParser\Node\Expr\Ternary as PhpTernary;
use PhpParser\Node\Scalar\Int_ as PhpInt;
use PhpParser\Node\Scalar\String_ as PhpString;
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
            foreach ($node->exprs as $expr) {
                foreach ($this->getMacroCalls($expr) as $macroCall) {
                    $this->fn->addToBody($macroCall);
                }
            }
        }

        return null;
    }

    /**
     * @return MacroCall[]
     */
    private function getMacroCalls(Node $node): array
    {
        if ($node instanceof PhpConcat) {
            return [...$this->getMacroCalls($node->left), ...$this->getMacroCalls($node->right)];
        } elseif ($node instanceof PhpTernary) {
            $condition = $this->getExpr($node->cond);
            $then = $this->getExpr($node->if);
            $else = $this->getExpr($node->else);
            if ($condition instanceof Expr && $then instanceof Expr && $else instanceof Expr) {
                return [new MacroCall('print', StrRef::placeholder(), new If_($condition, $then, $else))];
            }
        } elseif ($node instanceof PhpString) {
            return [PrintFactory::createWithString($node->value)];
        } elseif ($node instanceof PhpInt) {
            return [PrintFactory::createWithNumberValue($node->value)];
        } elseif (
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
            return new StrRef($node->value);
        }

        if ($node instanceof PhpInt) {
            return new Number($node->value);
        }

        if ($node instanceof PhpBinaryOp) {
            return $this->getPhpBinaryOpExpr($node);
        }

        if ($node instanceof PhpConstFetch) {
            return $this->getConstFetchExpr($node);
        }

        return null;
    }

    private function getPhpBinaryOpExpr(PhpBinaryOp $node): ?BinaryOp
    {
        $left = $this->getExpr($node->left);
        $right = $this->getExpr($node->right);
        if ($left instanceof Expr && $right instanceof Expr) {
            return new BinaryOp($node->getOperatorSigil(), $left, $right);
        }

        return null;
    }

    private function getConstFetchExpr(PhpConstFetch $node): ?Bool_
    {
        $name = $node->name->name;
        if ($name == 'true') {
            return new Bool_(true);
        }

        if ($name == 'false') {
            return new Bool_(false);
        }

        return null;
    }
}
