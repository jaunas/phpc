<?php

namespace Jaunas\PhpCompiler\Node\Expr;

class FnCall implements Expr
{
    private ?Expr $subject = null;

    /**
     * @param Expr[] $arguments
     */
    public function __construct(private readonly string $fnName, private readonly array $arguments = [])
    {
    }

    public function getSource(): string
    {
        $subjectPart = $this->subject instanceof Expr ? $this->subject->getSource() . '.' : '';
        $arguments = implode(', ', array_map(static fn($argument) => $argument->getSource(), $this->arguments));
        return sprintf("%s%s(%s)", $subjectPart, $this->fnName, $arguments);
    }

    public function setSubject(Expr $subject): void
    {
        $this->subject = $subject;
    }
}
