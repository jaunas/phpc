<?php

namespace Jaunas\PhpCompiler\Node\Expr;

class BinaryOp extends Expr
{
    public function __construct(
        private readonly string $sign,
        private readonly Number $left,
        private readonly Number $right
    ) {
    }

    public function print(): string
    {
        return sprintf("%s %s %s", $this->left->print(), $this->sign, $this->right->print());
    }
}
