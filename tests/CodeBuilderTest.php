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
    public function exitSyscallGetCode(): void
    {
        $builder = new CodeBuilder();
        $code = $builder
            ->addExitSyscall()
            ->getCode();
        $this->assertEquals("mov $60, %rax\nmov $0, %rdi\nsyscall\n", $code);
    }

    #[Test]
    public function entryPointAndExitSyscallGetCode(): void
    {
        $expectedCode = '.globl _start
_start:
  mov $60, %rax
  mov $0, %rdi
  syscall
';

        $builder = new CodeBuilder();
        $code = $builder
            ->beginEntryPoint('_start')
            ->addExitSyscall()
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
        $this->assertEquals("message:\n  .ascii \"Example text\"\nmessage_len = . - message\n", $code);
    }

    #[Test]
    public function addMultilineTextDataGetCode(): void
    {
        $expectedCode = 'message:
  .ascii "Example text\n"
  .ascii "With many lines\n"
  .ascii "\n"
  .ascii "And extra spaces"
message_len = . - message
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
        $this->assertEquals("message:\n  .ascii \"Example text\\n\"\nmessage_len = . - message\n", $code);
    }

    #[Test]
    public function addWriteSyscallGetCode(): void
    {
        $expectedCode = 'mov $1, %rax
mov $1, %rdi
lea message(%rip), %rsi
mov $message_len, %rdx
syscall
';

        $builder = new CodeBuilder();
        $code = $builder
            ->addWriteSyscall('message')
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
  mov $1, %rax
  mov $1, %rdi
  lea ascii_data0(%rip), %rsi
  mov $ascii_data0_len, %rdx
  syscall

  mov $1, %rax
  mov $1, %rdi
  lea ascii_data1(%rip), %rsi
  mov $ascii_data1_len, %rdx
  syscall

  mov $60, %rax
  mov $0, %rdi
  syscall
';

        $innerBuilder = new CodeBuilder();
        $innerBuilder
            ->addWriteSyscall('ascii_data0')
            ->addEmptyLine()
            ->addWriteSyscall('ascii_data1');

        $outerBuilder = new CodeBuilder();
        $code = $outerBuilder
            ->beginEntryPoint('entrypoint')
            ->addCodeBuilder($innerBuilder)
            ->addExitSyscall()
            ->getCode();

        $this->assertEquals($expectedCode, $code);
    }
}
