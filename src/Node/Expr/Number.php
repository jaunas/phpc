<?php

namespace Jaunas\PhpCompiler\Node\Expr;

class Number extends Expr
{
    public function __construct(private readonly int $value)
    {
    }

    public function print(): string
    {
        return "$this->value";
    }
}
