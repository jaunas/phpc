.section .data
echo0:
  .ascii "Example text\n"
echo0_len = . - echo0

.section .text
.globl _start
_start:
  mov $1, %rax
  mov $1, %rdi
  lea echo0(%rip), %rsi
  mov $echo0_len, %rdx
  syscall

  mov $60, %rax
  mov $0, %rdi
  syscall
