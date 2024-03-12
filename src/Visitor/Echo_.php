<?php

namespace Jaunas\PhpCompiler\Visitor;

use Jaunas\PhpCompiler\Node\Expr\Expr;
use Jaunas\PhpCompiler\Node\Expr\FnCall;
use Jaunas\PhpCompiler\Node\Expr\If_;
use Jaunas\PhpCompiler\Node\Expr\BinaryOp;
use Jaunas\PhpCompiler\Node\Expr\StrRef;
use Jaunas\PhpCompiler\Node\Expr\Value\Bool_;
use Jaunas\PhpCompiler\Node\Expr\Value\Number;
use Jaunas\PhpCompiler\Node\Expr\Value\String_;
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
            $expressions = [];

            foreach ($node->exprs as $phpExpr) {
                if ($phpExpr instanceof PhpExpr) {
                    $expr = $this->getExpr($phpExpr);
                    if ($expr instanceof Expr) {
                        $expressions[] = $expr;
                    }
                }
            }

            $placeholder = str_repeat('{}', count($expressions));
            $this->fn->addToBody(new MacroCall('print', new StrRef($placeholder), ...$expressions));
        }

        return null;
    }

    private function getExpr(?PhpExpr $node): ?Expr
    {
        if ($node instanceof PhpConcat) {
            return $this->getConcatExpr($node);
        }

        if ($node instanceof PhpTernary) {
            return $this->getIfExpr($node);
        }

        if ($node instanceof PhpString) {
            return String_::fromString($node->value);
        }

        if ($node instanceof PhpInt) {
            return new Number($node->value);
        }

        if ($node instanceof PhpBinaryOp) {
            return $this->getBinaryOpExpr($node);
        }

        if ($node instanceof PhpConstFetch) {
            return $this->getBoolExpr($node);
        }

        return null;
    }

    private function getConcatExpr(PhpConcat $node): ?FnCall
    {
        $left = $this->getExpr($node->left);
        $right = $this->getExpr($node->right);

        if (!$left instanceof Expr || !$right instanceof Expr) {
            return null;
        }

        $fnCall = new FnCall('concat', [$right]);
        $fnCall->setSubject($left);

        return $fnCall;
    }

    private function getIfExpr(PhpTernary $node): ?If_
    {
        $condition = $this->getExpr($node->cond);
        $then = $this->getExpr($node->if);
        $else = $this->getExpr($node->else);

        if (!$condition instanceof Expr || !$then instanceof Expr || !$else instanceof Expr) {
            return null;
        }

        return new If_($condition, $then, $else);
    }

    private function getBinaryOpExpr(PhpBinaryOp $node): ?BinaryOp
    {
        $left = $this->getExpr($node->left);
        $right = $this->getExpr($node->right);
        if ($left instanceof Expr && $right instanceof Expr) {
            return new BinaryOp($node->getOperatorSigil(), $left, $right);
        }

        return null;
    }

    private function getBoolExpr(PhpConstFetch $node): ?Bool_
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
