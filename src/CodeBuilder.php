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

    public function addExitCall(): self
    {
        return $this
            ->addLine('mov $0, %rdi')
            ->addLine('call exit');
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

        $lines = explode("\n", $text);
        foreach ($lines as $key => $line) {
            $type = $key == array_key_last($lines) ? 'asciz' : 'ascii';
            $this->addLine(sprintf(".%s \"%s\"", $type, $line));
        }
        $this->removeIndent();

        return $this;
    }

    public function addPrintfCall(string $textLabel, int $arg = null): self
    {
        $this->addLine(sprintf("lea %s(%%rip), %%rdi", $textLabel));

        if ($arg !== null) {
            $this->addLine(sprintf("mov \$%d, %%rsi", $arg));
        }

        return $this->addLine('call printf');
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
