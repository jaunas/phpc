<?php

namespace Jaunas\PhpCompiler\Node\Expr;

readonly class PhpNumber implements Expr
{
    public function __construct(private Expr $expr)
    {
    }

    public function getSource(): string
    {
        return sprintf("rust_php::PhpNumber::new(%s)", $this->expr->getSource());
    }
}
