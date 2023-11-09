<?php

namespace Jaunas\PhpCompiler;

use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\InlineHTML;

class Compiler
{

    /**
     * @param Stmt[] $ast
     */
    public function __construct(private readonly array $ast)
    {
    }

    public function compile(): string
    {
        $builder = new CodeBuilder();

        $hasInlineHtml = false;
        foreach ($this->ast as $stmt) {
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

        return $builder->getCode();
    }
}
