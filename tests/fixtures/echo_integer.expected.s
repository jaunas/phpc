.section .data
int2str:
  .asciz "%lld"

.section .text
.globl _start
_start:
  lea int2str(%rip), %rdi
  mov $201182, %rsi
  call printf

  mov $0, %rdi
  call exit
