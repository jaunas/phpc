<?php

namespace Jaunas\PhpCompiler\Node\Expr\Value;

use Jaunas\PhpCompiler\Node\Expr\Expr;
use Jaunas\PhpCompiler\Node\Expr\StrRef;

readonly class String_ implements Expr
{
    public function __construct(private readonly StrRef $content)
    {
    }

    public function getSource(): string
    {
        return sprintf("rust_php::Value::String(%s.to_string())", $this->content->getSource());
    }

    public static function fromString(string $string): self
    {
        return new String_(new StrRef($string));
    }
}
