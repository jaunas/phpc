<?php

namespace Jaunas\PhpCompiler\Node\Expr;

class BinaryOp extends ArithmeticExpr
{
    public function __construct(
        private readonly string $sign,
        private readonly ArithmeticExpr $left,
        private readonly ArithmeticExpr $right
    ) {
    }

    public function getSource(): string
    {
        $left = $this->left instanceof BinaryOp
            ? sprintf("(%s)", $this->left->getSource())
            : $this->left->getSource();
        $right = $this->right instanceof BinaryOp
            ? sprintf("(%s)", $this->right->getSource())
            : $this->right->getSource();
        return sprintf("%s %s %s", $left, $this->sign, $right);
    }
}
