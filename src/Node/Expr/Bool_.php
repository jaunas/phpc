<?php

namespace Jaunas\PhpCompiler\Node\Expr;

readonly class Bool_ implements Expr
{
    public function __construct(private bool $value)
    {
    }

    public function getSource(): string
    {
        return $this->value ? 'true' : 'false';
    }
}
