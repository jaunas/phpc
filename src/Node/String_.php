<?php

namespace Jaunas\PhpCompiler\Node;

readonly class String_ implements Node
{
    public function __construct(private string $content)
    {
    }

    public function print(): string
    {
        return sprintf('"%s"', $this->content);
    }
}
