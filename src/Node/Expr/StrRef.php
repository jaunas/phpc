<?php

namespace Jaunas\PhpCompiler\Node\Expr;

readonly class StrRef implements Expr
{
    public function __construct(private string $content)
    {
    }

    public function getSource(): string
    {
        return sprintf('"%s"', $this->content);
    }

    public static function placeholder(): self
    {
        return new StrRef('{}');
    }
}
