.section .data
html_inline0:
  .asciz "Inline header\n"
echo0:
  .asciz "Echo in a new line\n"
html_inline1:
  .asciz "Mixed "
echo1:
  .asciz "echo"
html_inline2:
  .asciz " with inline\n"

.section .text
.globl _start
_start:
  lea html_inline0(%rip), %rdi
  call printf

  lea echo0(%rip), %rdi
  call printf

  lea html_inline1(%rip), %rdi
  call printf

  lea echo1(%rip), %rdi
  call printf

  lea html_inline2(%rip), %rdi
  call printf

  mov $0, %rdi
  call exit
