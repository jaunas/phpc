.section .data
echo:
  .ascii "Example text\n"
echo_len = . - echo

.section .text
.globl _start
_start:
  mov $1, %rax
  mov $1, %rdi
  lea echo(%rip), %rsi
  mov $echo_len, %rdx
  syscall

  mov $60, %rax
  mov $0, %rdi
  syscall
