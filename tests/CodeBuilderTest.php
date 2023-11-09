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
    public function builderWithStartEntryPointGetCode(): void
    {
        $builder = new CodeBuilder();
        $code = $builder
            ->beginEntryPoint('_start')
            ->getCode();
        $this->assertEquals(".globl _start\n_start:\n", $code);
    }

    #[Test]
    public function builderWithExitCallGetCode(): void
    {
        $builder = new CodeBuilder();
        $code = $builder
            ->addExitCall()
            ->getCode();
        $this->assertEquals("mov $60, %rax\nmov $0, %rdi\nsyscall\n", $code);
    }

    #[Test]
    public function builderWithEntryPointAndExitCallGetCode(): void
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
            ->addExitCall()
            ->getCode();
        $this->assertEquals($expectedCode, $code);

    }
}
