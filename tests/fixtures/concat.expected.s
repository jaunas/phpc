.section .data
echo0:
  .asciz "splitted"
echo1:
  .asciz " string\n"

.section .text
.globl _start
_start:
  lea echo0(%rip), %rdi
  call printf

  lea echo1(%rip), %rdi
  call printf

  mov $0, %rdi
  call exit
