<?php

namespace Jaunas\PhpCompiler\Visitor;

use Jaunas\PhpCompiler\Node\Expr\Number as RustNumber;
use Jaunas\PhpCompiler\Node\Expr\BinaryOp as RustBinaryOp;
use Jaunas\PhpCompiler\Node\Expr\PhpNumber as RustPhpNumber;
use Jaunas\PhpCompiler\Node\Expr\String_ as RustString;
use Jaunas\PhpCompiler\Node\Fn_ as RustFn;
use Jaunas\PhpCompiler\Node\MacroCall as RustMacroCall;
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
            $this->fn->addToBody(new RustMacroCall('print', new RustString($node->value)));
        } elseif ($node instanceof PhpLNumber) {
            $this->fn->addToBody(new RustMacroCall(
                'print',
                new RustString('{}'),
                new RustPhpNumber(new RustNumber($node->value))
            ));
        } elseif ($node instanceof PhpConcat) {
            $this->enterExpr($node->left);
            $this->enterExpr($node->right);
        } elseif ($node instanceof PhpBinaryOp) {
            if ($node->left instanceof PhpLNumber && $node->right instanceof PhpLNumber) {
                $this->fn->addToBody(
                    new RustMacroCall(
                        'print',
                        new RustString('{}'),
                        new RustPhpNumber(new RustBinaryOp(
                            $node->getOperatorSigil(),
                            new RustNumber($node->left->value),
                            new RustNumber($node->right->value)
                        ))
                    )
                );
            }
        }
    }
}
