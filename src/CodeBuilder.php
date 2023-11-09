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

    private function setIndent(): self
    {
        $this->indent = true;
        return $this;
    }

    private function removeIndent(): self
    {
        $this->indent = false;
        return $this;
    }

    public function beginEntryPoint(string $label): self
    {
        $this
            ->addLine(sprintf('.globl %s', $label))
            ->addLine(sprintf('%s:', $label))
            ->setIndent();
        return $this;
    }

    public function addExitSyscall(): self
    {
        $this
            ->addLine('mov $60, %rax')
            ->addLine('mov $0, %rdi')
            ->addLine('syscall');
        return $this;
    }

    public function addSection(string $section): self
    {
        $this->addLine(sprintf('.section %s', $section));
        return $this;
    }

    public function addTextData(string $label, string $text): self
    {
        $text = str_replace("\n", "\\n\n", $text);
        $text = rtrim($text, "\n");

        $this
            ->addLine(sprintf("%s:", $label))
            ->setIndent();
        foreach (explode("\n", $text) as $line) {
            $this->addLine(sprintf(".ascii \"%s\"", $line));
        }
        $this
            ->removeIndent()
            ->addLine(sprintf("%s_len = . - %s", $label, $label));
        return $this;
    }

    public function addWriteSyscall(string $textLabel): self
    {
        $this
            ->addLine('mov $1, %rax')
            ->addLine('mov $1, %rdi')
            ->addLine(sprintf("lea %s(%%rip), %%rsi", $textLabel))
            ->addLine(sprintf("mov $%s_len, %%rdx", $textLabel))
            ->addLine('syscall');
        return $this;
    }

    public function addEmptyLine(): self
    {
        $oldIndent = $this->indent;
        $this->indent = false;

        $this->addLine('');

        $this->indent = $oldIndent;
        return $this;
    }
}
