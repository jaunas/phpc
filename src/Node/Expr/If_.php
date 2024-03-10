<?php

namespace Jaunas\PhpCompiler\Node\Expr;

use Jaunas\PhpCompiler\Node\Expr\Value\Value;

readonly class If_ implements Expr
{
    private Expr $condition;

    public function __construct(
        Expr $condition,
        private Expr $then,
        private Expr $else
    ) {
        if ($condition instanceof Value) {
            $fnCall = new FnCall('to_bool');
            $fnCall->setSubject($condition);
            $condition = $fnCall;
        }

        $this->condition = $condition;
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
