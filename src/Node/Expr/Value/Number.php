<?php

namespace Jaunas\PhpCompiler\Node\Expr\Value;

readonly class Number implements Value
{
    public function __construct(private int|float $value)
    {
    }

    public function getSource(): string
    {
        return sprintf("Value::Number(%s_f64)", $this->value);
    }
}
