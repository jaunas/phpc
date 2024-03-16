<?php

namespace Jaunas\PhpCompiler\Node;

readonly class Use_ implements Node
{
    /**
     * @param string[] $path
     */
    public function __construct(private array $path)
    {
    }

    public function getSource(): string
    {
        return sprintf("use %s;\n", implode('::', $this->path));
    }
}
