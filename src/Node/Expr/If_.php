<?php

namespace Jaunas\PhpCompiler\Node\Expr;

class If_ extends Expr
{
    public function __construct(
        private readonly Expr $condition,
        private readonly Expr $then,
        private readonly Expr $else
    ) {
    }

    public function getSource(): string
    {
        return sprintf(
            'if %s { %s } else { %s }',
            $this->condition->getSource(),
            $this->then->getSource(),
            $this->else->getSource()
        );
    }
}
