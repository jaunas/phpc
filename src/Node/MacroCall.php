<?php

namespace Jaunas\PhpCompiler\Node;

readonly class MacroCall implements Node
{
    public function __construct(private string $name, private ?String_ $argument = null)
    {
    }

    public function print(): string
    {
        return sprintf("%s!(%s);\n", $this->name, $this->argument?->print() ?? '');
    }
}
