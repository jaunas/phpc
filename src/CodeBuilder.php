<?php

namespace Jaunas\PhpCompiler;

class CodeBuilder
{

    private string $code = '';
    private bool $indent = false;

    public function getCode(): string
    {
        return $this->code;
    }

    private function addLine(string $line): self
    {
        $this->code .= ($this->indent ? '  ' : '') . $line . "\n";
        return $this;
    }

    public function beginEntryPoint(string $label): self
    {
        $this
            ->addLine(sprintf('.globl %s', $label))
            ->addLine(sprintf('%s:', $label));
        $this->indent = true;
        return $this;
    }

    public function addExitCall(): self
    {
        $this
            ->addLine('mov $60, %rax')
            ->addLine('mov $0, %rdi')
            ->addLine('syscall');
        return $this;
    }
}
