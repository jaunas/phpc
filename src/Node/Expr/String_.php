<?php

namespace Jaunas\PhpCompiler\Node\Expr;

class String_ extends Expr
{
    public function __construct(private readonly string $content)
    {
    }

    public function getSource(): string
    {
        return sprintf('"%s"', $this->content);
    }
}
