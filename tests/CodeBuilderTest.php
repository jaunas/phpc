<?php

namespace Jaunas\PhpCompiler\Tests;

use Jaunas\PhpCompiler\CodeBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CodeBuilder::class)]
class CodeBuilderTest extends TestCase
{
    #[Test]
    public function emptyBuilderGetCode(): void
    {
        $builder = new CodeBuilder();
        $this->assertEmpty($builder->getCode());
    }

    #[Test]
    public function startEntryPointGetCode(): void
    {
        $builder = new CodeBuilder();
        $code = $builder
            ->beginEntryPoint('_start')
            ->getCode();
        $this->assertEquals(".globl _start\n_start:\n", $code);
    }

    #[Test]
    public function exitCallGetCode(): void
    {
        $builder = new CodeBuilder();
        $code = $builder
            ->addExitCall()
            ->getCode();
        $this->assertEquals("mov $0, %rdi\ncall exit\n", $code);
    }

    #[Test]
    public function entryPointAndExitSyscallGetCode(): void
    {
        $expectedCode = ".globl _start\n_start:\n  mov \$0, %rdi\n  call exit\n";

        $builder = new CodeBuilder();
        $code = $builder
            ->beginEntryPoint('_start')
            ->addExitCall()
            ->getCode();
        $this->assertEquals($expectedCode, $code);
    }

    #[Test]
    public function addSectionGetCode(): void
    {
        $builder = new CodeBuilder();
        $code = $builder
            ->addSection('.data')
            ->getCode();
        $this->assertEquals(".section .data\n", $code);
    }

    #[Test]
    public function addTextDataGetCode(): void
    {
        $builder = new CodeBuilder();
        $code = $builder
            ->addTextData('message', 'Example text')
            ->getCode();
        $this->assertEquals("message:\n  .asciz \"Example text\"\n", $code);
    }

    #[Test]
    public function addMultilineTextDataGetCode(): void
    {
        $expectedCode = 'message:
  .ascii "Example text\n"
  .ascii "With many lines\n"
  .ascii "\n"
  .asciz "And extra spaces"
';

        $builder = new CodeBuilder();
        $code = $builder
            ->addTextData('message', "Example text\nWith many lines\n\nAnd extra spaces")
            ->getCode();
        $this->assertEquals($expectedCode, $code);
    }

    #[Test]
    public function addTextDataWithNewLineOnTheEnd(): void
    {
        $builder = new CodeBuilder();
        $code = $builder
            ->addTextData('message', "Example text\n")
            ->getCode();
        $this->assertEquals("message:\n  .asciz \"Example text\\n\"\n", $code);
    }

    #[Test]
    public function addPrintfCallGetCode(): void
    {
        $expectedCode = "lea message(%rip), %rdi\ncall printf\n";

        $builder = new CodeBuilder();
        $code = $builder
            ->addPrintfCall('message')
            ->getCode();

        $this->assertEquals($expectedCode, $code);
    }

    #[Test]
    public function addPrintfCallWithArgGetCode(): void
    {
        $expectedCode = "lea message(%rip), %rdi\nmov $31415926, %rsi\ncall printf\n";

        $builder = new CodeBuilder();
        $code = $builder
            ->addPrintfCall('message', 31415926)
            ->getCode();

        $this->assertEquals($expectedCode, $code);
    }

    #[Test]
    public function addEmptyLineGetCode(): void
    {
        $builder = new CodeBuilder();
        $code = $builder
            ->addEmptyLine()
            ->getCode();

        $this->assertEquals("\n", $code);
    }

    #[Test]
    public function addEmptyLineAlwaysNoIndentGetCode(): void
    {
        $builder = new CodeBuilder();
        $code = $builder
            ->beginEntryPoint('entrypoint')
            ->addEmptyLine()
            ->getCode();

        $this->assertEquals(".globl entrypoint\nentrypoint:\n\n", $code);
    }

    #[Test]
    public function addEmptyCodeBuilderGetCode(): void
    {
        $outerBuilder = new CodeBuilder();
        $innerBuilder = new CodeBuilder();
        $code = $outerBuilder
            ->addCodeBuilder($innerBuilder)
            ->getCode();

        $this->assertEmpty($code);
    }

    #[Test]
    public function addCodeBuilderGetCode(): void
    {
        $expectedCode = '.globl entrypoint
entrypoint:
  lea ascii_data0(%rip), %rdi
  call printf

  lea ascii_data1(%rip), %rdi
  call printf

  mov $0, %rdi
  call exit
';

        $innerBuilder = new CodeBuilder();
        $innerBuilder
            ->addPrintfCall('ascii_data0')
            ->addEmptyLine()
            ->addPrintfCall('ascii_data1');

        $outerBuilder = new CodeBuilder();
        $code = $outerBuilder
            ->beginEntryPoint('entrypoint')
            ->addCodeBuilder($innerBuilder)
            ->addExitCall()
            ->getCode();

        $this->assertEquals($expectedCode, $code);
    }
}
