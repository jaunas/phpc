<?php

namespace Jaunas\PhpCompiler\Node;

class Mod implements Node
{
    /** @var Node[] */
    private array $statements = [];

    public function getSource(): string
    {
        return implode('', array_map(static fn($statement) => $statement->getSource(), $this->statements));
    }

    public function addStatement(Node $statement): void
    {
        $this->statements[] = $statement;
    }
}
