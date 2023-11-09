<?php

namespace Jaunas\PhpCompiler;

use Jaunas\PhpCompiler\Exception\FileNotFound;

class App
{
    private string $filename;

    public function __construct(array $argv)
    {
        $this->filename = $argv[1];
    }

    public function compile(): void
    {
        if (!file_exists($this->filename)) {
            throw new FileNotFound();
        }

        $builder = new CodeBuilder();
        $code = $builder
            ->beginEntryPoint('_start')
            ->addExitCall()
            ->getCode();

        file_put_contents($this->getCompiledFilename(), $code);
    }

    public function getCompiledFilename(): string
    {
        return dirname($this->filename) . '/' . pathinfo($this->filename, PATHINFO_FILENAME) . '.s';
    }
}
