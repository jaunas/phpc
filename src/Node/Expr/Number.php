<?php

namespace Jaunas\PhpCompiler\Node\Expr;

readonly class Number implements ArithmeticExpr
{
    public function __construct(private int $value)
    {
    }

    public function getSource(): string
    {
        return $this->value . '_f64';
    }
}
