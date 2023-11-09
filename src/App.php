<?php

namespace Jaunas\PhpCompiler;

use Jaunas\PhpCompiler\Exception\FileNotFound;
use PhpParser\ParserFactory;

class App
{
    private string $filename;

    public function __construct(array $argv)
    {
        $this->filename = $argv[1];
    }

    public function generateCompiledScript(): void
    {
        if (!file_exists($this->filename)) {
            throw new FileNotFound();
        }

        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        $ast = $parser->parse(file_get_contents($this->filename));
        $compiler = new Compiler($ast);

        file_put_contents($this->getCompiledFilename(), $compiler->compile());
    }

    public function getCompiledFilename(): string
    {
        return dirname($this->filename) . '/' . pathinfo($this->filename, PATHINFO_FILENAME) . '.s';
    }
}
