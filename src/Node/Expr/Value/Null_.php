<?php

namespace Jaunas\PhpCompiler\Node\Expr\Value;

use Jaunas\PhpCompiler\Node\Expr\Expr;

class Null_ implements Expr
{
    public function getSource(): string
    {
        return 'rust_php::Value::Null';
    }
}
