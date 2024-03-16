<?php

namespace Jaunas\PhpCompiler\Node\Expr\Value;

readonly class Bool_ implements Value
{
    public function __construct(private bool $value)
    {
    }

    public function getSource(): string
    {
        return sprintf("Value::Bool(%s)", $this->value ? 'true' : 'false');
    }
}
