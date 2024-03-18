<?php

namespace Jaunas\PhpCompiler\Node\Expr;

readonly class FunctionCall implements Expr
{
    /**
     * @param Expr[] $args
     */
    public function __construct(private string $functionName, private array $args)
    {
    }

    public function getSource(): string
    {
        $args = implode(', ', array_map(static fn($value) => $value->getSource(), $this->args));
        return sprintf("functions::%s::call(vec![%s]).unwrap()", $this->functionName, $args);
    }
}
