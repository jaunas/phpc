<?php

namespace Jaunas\PhpCompiler\Node;

readonly class Number implements Node
{
    public function __construct(private int $value)
    {
    }

    public function print(): string
    {
        return "$this->value";
    }
}
