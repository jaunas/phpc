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

    /**
     * @throws FileNotFound
     */
    public function generateCompiledScript(): void
    {
        $this->throwExceptionWhenNoFile();

        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        $ast = $parser->parse(file_get_contents($this->filename));
        $compiler = new Compiler($ast);

        file_put_contents($this->getCompiledFilename('s'), $compiler->compile());
    }

    /**
     * @throws FileNotFound
     */
    public function generateTranslatedScript(): void
    {
        $this->throwExceptionWhenNoFile();

        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        $ast = $parser->parse(file_get_contents($this->filename));
        $translator = new Translator();

        file_put_contents($this->getCompiledFilename('rs'), $translator->translate($ast)->print());
    }

    private function getCompiledFilename(string $extension): string
    {
        $filename = pathinfo($this->filename, PATHINFO_FILENAME);
        return sprintf("%s/%s.%s", dirname($this->filename), $filename, $extension);
    }

    /**
     * @throws FileNotFound
     */
    private function throwExceptionWhenNoFile(): void
    {
        if (!file_exists($this->filename)) {
            throw new FileNotFound();
        }
    }
}
