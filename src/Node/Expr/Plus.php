<?php

namespace Jaunas\PhpCompiler\Node\Expr;

class Plus extends Expr
{
    public function __construct(private readonly Number $left, private readonly Number $right)
    {
    }

    public function print(): string
    {
        return sprintf("%s + %s", $this->left->print(), $this->right->print());
    }
}
