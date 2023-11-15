.section .data
echo0:
  .asciz "Example text\n"

.section .text
.globl _start
_start:
  lea echo0(%rip), %rdi
  call printf

  mov $0, %rdi
  call exit
