<?php

namespace Jaunas\PhpCompiler\Node\Expr;

readonly class BinaryOp implements Expr
{
    public function __construct(
        private string $sign,
        private Expr $left,
        private Expr $right
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
