<?php

namespace Jaunas\PhpCompiler\Node\Expr\Value;

readonly class Null_ implements Value
{
    public function getSource(): string
    {
        return 'Value::Null';
    }
}
