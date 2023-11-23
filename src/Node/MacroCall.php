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

    public function print(): string
    {
        $arguments = [];
        if ($this->format !== null) {
            $arguments[] = $this->format->print();
        }
        if ($this->argument !== null) {
            $arguments[] = $this->argument->print();
        }

        return sprintf("%s!(%s);\n", $this->name, implode(', ', $arguments));
    }
}
