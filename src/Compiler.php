<?php

namespace Jaunas\PhpCompiler;

use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Stmt\InlineHTML;

class Compiler
{
    private CodeBuilder $builder;
    private array $data = [];
    private array $codeParts = [];
    private int $htmlInlineCount = 0;
    private int $echoCount = 0;

    /**
     * @param Stmt[] $ast
     */
    public function __construct(private readonly array $ast)
    {
    }

    public function compile(): string
    {
        $this->builder = new CodeBuilder();

        $this->processAst();
        $this->appendDataSection();
        $this->appendCodeSection();

        return $this->builder->getCode();
    }

    private function hasDataSection(): bool {
        return !empty($this->data);
    }

    private function processAst(): void
    {
        foreach ($this->ast as $stmt) {
            if ($stmt instanceof InlineHTML) {
                $this->addHtmlInline($stmt);
            } else if ($stmt instanceof Echo_) {
                $this->addEcho($stmt);
            }
        }
    }

    private function appendDataSection(): void
    {
        if ($this->hasDataSection()) {
            $this->builder->addSection('.data');
            foreach ($this->data as $label => $value) {
                $this->builder->addTextData($label, $value);
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
        if ($this->hasDataSection()) {
            $this->builder
                ->addEmptyLine()
                ->addSection('.text');
        }
        $this->builder->beginEntryPoint('_start');
    }

    private function appendCodeSectionBody(): void
    {
        foreach ($this->codeParts as $codePart) {
            $this->builder->addCodeBuilder($codePart);
        }
        $this->builder->addExitSyscall();
    }

    private function addHtmlInline(InlineHTML $stmt): void
    {
        $label = 'html_inline' . $this->htmlInlineCount;
        $this->data[$label] = $stmt->value;
        $this->codeParts[] = (new CodeBuilder())->addWriteSyscall($label);
        $this->htmlInlineCount++;
    }

    private function addEcho(Echo_ $stmt): void
    {
        $expr = $stmt->exprs[0];
        if ($expr instanceof String_) {
            $label = 'echo' . $this->echoCount;
            $this->data[$label] = $expr->value;
            $this->codeParts[] = (new CodeBuilder())->addWriteSyscall($label);
            $this->echoCount++;
        }
    }
}
