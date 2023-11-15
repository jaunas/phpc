.section .data
echo0:
  .asciz "Example text\n"
echo1:
  .asciz "No end line"
echo2:
  .asciz " continue here\n"

.section .text
.globl _start
_start:
  lea echo0(%rip), %rdi
  call printf

  lea echo1(%rip), %rdi
  call printf

  lea echo2(%rip), %rdi
  call printf

  mov $0, %rdi
  call exit
