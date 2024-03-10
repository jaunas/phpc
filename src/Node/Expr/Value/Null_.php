<?php

namespace Jaunas\PhpCompiler\Node\Expr\Value;

readonly class Null_ implements Value
{
    public function getSource(): string
    {
        return 'rust_php::Value::Null';
    }
}
