<?php

namespace Jaunas\PhpCompiler\Node;

use Jaunas\PhpCompiler\Node\Expr\Expr;
use Jaunas\PhpCompiler\Node\Expr\StrRef;

readonly class MacroCall implements Node
{
    /** @var Expr[] */
    private array $arguments;

    public function __construct(
        private string $name,
        private ?StrRef $format = null,
        Expr ...$arguments,
    ) {
        $this->arguments = $arguments;
    }

    public function getSource(): string
    {
        $arguments = [];
        if ($this->format instanceof StrRef) {
            $arguments[] = $this->format->getSource();
        }

        foreach ($this->arguments as $argument) {
            $arguments[] = $argument->getSource();
        }

        return sprintf("%s!(%s);\n", $this->name, implode(', ', $arguments));
    }
}
