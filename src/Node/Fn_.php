<?php

namespace Jaunas\PhpCompiler\Node;

use Jaunas\PhpCompiler\Node\Expr\Expr;

class Fn_ implements Node
{
    /** @var Expr[] */
    private array $body = [];

    public function __construct(private readonly string $name)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addStatement(Expr $statement): void
    {
        $this->body[] = $statement;
    }

    /**
     * @return Expr[]
     */
    public function getBody(): array
    {
        return $this->body;
    }

    public function getSource(): string
    {
        $body = '';
        foreach ($this->body as $statement) {
            $body .= $statement->getSource() . ";\n";
        }

        return "fn {$this->getName()}() {\n{$body}}\n";
    }
}
