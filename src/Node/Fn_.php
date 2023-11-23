<?php

namespace Jaunas\PhpCompiler\Node;

class Fn_ implements Node
{
    /**
     * @var MacroCall[]
     */
    private array $body = [];

    public function __construct(private readonly string $name)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addToBody(MacroCall $println): void
    {
        $this->body[] = $println;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function print(): string
    {
        $body = '';
        foreach ($this->body as $macroCall) {
            $body .= $macroCall->print();
        }
        return "fn {$this->getName()}() {\n$body}\n";
    }
}
