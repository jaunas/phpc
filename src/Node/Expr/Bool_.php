<?php

namespace Jaunas\PhpCompiler\Node\Expr;

class Bool_ extends Expr
{
    public function __construct(private readonly bool $value)
    {
    }

    public function getSource(): string
    {
        return $this->value ? 'true' : 'false';
    }
}
