<?php

namespace Jaunas\PhpCompiler;

use Jaunas\PhpCompiler\Exception\FileNotReadable;
use PhpParser\ParserFactory;

class App
{
    private string $filename;

    /**
     * @param string[] $argv
     */
    public function __construct(array $argv)
    {
        $this->filename = $argv[1];
    }

    /**
     * @throws FileNotReadable
     */
    public function generateTranslatedScript(): void
    {
        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        $ast = $parser->parse($this->readPhpCode()) ?? [];
        $translator = new Translator();

        file_put_contents($this->getCompiledFilename(), $translator->translate($ast)->print());
    }

    private function getCompiledFilename(): string
    {
        $filename = pathinfo($this->filename, PATHINFO_FILENAME);
        return sprintf("%s/%s.%s", dirname($this->filename), $filename, 'rs');
    }

    /**
     * @throws FileNotReadable
     */
    private function readPhpCode(): string
    {
        $phpCode = file_exists($this->filename) ? file_get_contents($this->filename) : false;
        if ($phpCode === false) {
            throw new FileNotReadable();
        }

        return $phpCode;
    }
}
