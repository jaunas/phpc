<?php

namespace Jaunas\PhpCompiler\Node\Expr;

class Number extends ArithmeticExpr
{
    public function __construct(private readonly int $value)
    {
    }

    public function getSource(): string
    {
        return $this->value . '_f64';
    }
}
