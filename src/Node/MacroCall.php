<?php

namespace Jaunas\PhpCompiler\Node;

readonly class MacroCall implements Node
{
    public function __construct(
        private string $name,
        private ?String_ $format = null,
        private ?Node $argument = null
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
