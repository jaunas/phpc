<?php

namespace Jaunas\PhpCompiler\Node\Expr\Value;

readonly class Concat implements Value
{
    public function __construct(private Value $value, private Value $argument)
    {
    }

    public function getSource(): string
    {
        return sprintf("%s.concat(%s)", $this->value->getSource(), $this->argument->getSource());
    }
}
