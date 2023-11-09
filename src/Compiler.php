<?php

namespace Jaunas\PhpCompiler;

use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\InlineHTML;

class Compiler
{
    private CodeBuilder $builder;
    private bool $hasDataSection = false;
    private bool $hasInlineHtml = false;

    /**
     * @param Stmt[] $ast
     */
    public function __construct(private readonly array $ast)
    {
    }

    public function compile(): string
    {
        $this->builder = new CodeBuilder();

        $this->appendDataSection();
        $this->appendCodeSection();

        return $this->builder->getCode();
    }

    private function appendDataSection(): void
    {
        foreach ($this->ast as $stmt) {
            if ($stmt instanceof InlineHTML) {
                $this->hasDataSection = true;
                $this->hasInlineHtml = true;
                $this->builder
                    ->addSection('.data')
                    ->addTextData('html_inline', $stmt->value);
            }
        }
    }

    private function appendCodeSection(): void
    {
        $this->appendCodeSectionHeader();
        $this->appendCodeSectionBody();
    }

    private function appendCodeSectionHeader(): void
    {
        if ($this->hasDataSection) {
            $this->builder
                ->addEmptyLine()
                ->addSection('.text');
        }
        $this->builder->beginEntryPoint('_start');
    }

    private function appendCodeSectionBody(): void
    {
        if ($this->hasInlineHtml) {
            $this->builder
                ->addWriteSyscall('html_inline')
                ->addEmptyLine();
        }
        $this->builder->addExitSyscall();
    }
}
