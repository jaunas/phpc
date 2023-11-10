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
        $indent = $this->indent && !(empty($line)) ? '  ' : '';
        $this->code .= sprintf("%s%s\n", $indent, $line);
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
        return $this
            ->addLine(sprintf('.globl %s', $label))
            ->addLine(sprintf('%s:', $label))
            ->setIndent();
    }

    public function addExitSyscall(): self
    {
        return $this
            ->addLine('mov $60, %rax')
            ->addLine('mov $0, %rdi')
            ->addLine('syscall');
    }

    public function addSection(string $section): self
    {
        return $this->addLine(sprintf('.section %s', $section));
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
        return $this
            ->addLine('mov $1, %rax')
            ->addLine('mov $1, %rdi')
            ->addLine(sprintf("lea %s(%%rip), %%rsi", $textLabel))
            ->addLine(sprintf("mov $%s_len, %%rdx", $textLabel))
            ->addLine('syscall');
    }

    public function addEmptyLine(): self
    {
        return $this->addLine('');
    }

    public function addCodeBuilder(CodeBuilder $innerBuilder): self
    {
        $code = $innerBuilder->getCode();
        if (empty($code)) {
            return $this;
        }

        foreach (explode("\n", $code) as $line) {
            $this->addLine($line);
        }
        return $this;
    }
}
