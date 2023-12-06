<?php

namespace Jaunas\PhpCompiler\Node;

use Jaunas\PhpCompiler\Node\Expr\Expr;
use Jaunas\PhpCompiler\Node\Expr\String_;

readonly class MacroCall implements Node
{
    public function __construct(
        private string $name,
        private ?String_ $format = null,
        private ?Expr $argument = null
    ) {
    }

    public function getSource(): string
    {
        $arguments = [];
        if ($this->format instanceof String_) {
            $arguments[] = $this->format->getSource();
        }

        if ($this->argument instanceof Expr) {
            $arguments[] = $this->argument->getSource();
        }

        return sprintf("%s!(%s);\n", $this->name, implode(', ', $arguments));
    }
}
