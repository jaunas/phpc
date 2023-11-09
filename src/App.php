<?php

namespace Jaunas\PhpCompiler;

use Jaunas\PhpCompiler\Exception\FileNotFound;
use PhpParser\Node\Stmt\InlineHTML;
use PhpParser\ParserFactory;

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

        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        $ast = $parser->parse(file_get_contents($this->filename));
        $hasInlineHtml = false;
        foreach ($ast as $stmt) {
            if ($stmt instanceof InlineHTML) {
                $hasInlineHtml = true;
                $builder
                    ->addSection('.data')
                    ->addTextData('html_inline', $stmt->value);
            }
        }

        if ($hasInlineHtml) {
            $builder
                ->addEmptyLine()
                ->addSection('.text')
                ->beginEntryPoint('_start')
                ->addWriteSyscall('html_inline')
                ->addEmptyLine();
        } else {
            $builder->beginEntryPoint('_start');
        }
        $builder->addExitSyscall();

        file_put_contents($this->getCompiledFilename(), $builder->getCode());
    }

    public function getCompiledFilename(): string
    {
        return dirname($this->filename) . '/' . pathinfo($this->filename, PATHINFO_FILENAME) . '.s';
    }
}
