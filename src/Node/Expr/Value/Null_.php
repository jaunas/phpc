<?php

namespace Jaunas\PhpCompiler\Node\Expr\Value;

use Jaunas\PhpCompiler\Node\Expr\Expr;

class Null_ extends Expr
{
    public function getSource(): string
    {
        return 'rust_php::Value::Null';
    }
}
