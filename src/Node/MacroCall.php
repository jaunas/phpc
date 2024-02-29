<?php

namespace Jaunas\PhpCompiler\Node;

use Jaunas\PhpCompiler\Node\Expr\Expr;
use Jaunas\PhpCompiler\Node\Expr\StrRef;

readonly class MacroCall implements Node
{
    public function __construct(
        private string $name,
        private ?StrRef $format = null,
        private ?Expr $argument = null
    ) {
    }

    public function getSource(): string
    {
        $arguments = [];
        if ($this->format instanceof StrRef) {
            $arguments[] = $this->format->getSource();
        }

        if ($this->argument instanceof Expr) {
            $arguments[] = $this->argument->getSource();
        }

        return sprintf("%s!(%s);\n", $this->name, implode(', ', $arguments));
    }
}
