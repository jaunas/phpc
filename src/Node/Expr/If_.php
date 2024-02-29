<?php

namespace Jaunas\PhpCompiler\Node\Expr;

readonly class If_ implements Expr
{
    public function __construct(
        private Expr $condition,
        private Expr $then,
        private Expr $else
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
