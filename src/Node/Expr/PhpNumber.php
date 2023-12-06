<?php

namespace Jaunas\PhpCompiler\Node\Expr;

class PhpNumber extends Expr
{
    public function __construct(private readonly Expr $expr)
    {
    }

    public function getSource(): string
    {
        return sprintf("rust_php::PhpNumber::new(%s)", $this->expr->getSource());
    }
}
