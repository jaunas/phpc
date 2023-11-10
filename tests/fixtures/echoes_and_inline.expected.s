.section .data
html_inline0:
  .ascii "Inline header\n"
html_inline0_len = . - html_inline0
echo0:
  .ascii "Echo in a new line\n"
echo0_len = . - echo0
html_inline1:
  .ascii "Mixed "
html_inline1_len = . - html_inline1
echo1:
  .ascii "echo"
echo1_len = . - echo1
html_inline2:
  .ascii " with inline\n"
html_inline2_len = . - html_inline2

.section .text
.globl _start
_start:
  mov $1, %rax
  mov $1, %rdi
  lea html_inline0(%rip), %rsi
  mov $html_inline0_len, %rdx
  syscall

  mov $1, %rax
  mov $1, %rdi
  lea echo0(%rip), %rsi
  mov $echo0_len, %rdx
  syscall

  mov $1, %rax
  mov $1, %rdi
  lea html_inline1(%rip), %rsi
  mov $html_inline1_len, %rdx
  syscall

  mov $1, %rax
  mov $1, %rdi
  lea echo1(%rip), %rsi
  mov $echo1_len, %rdx
  syscall

  mov $1, %rax
  mov $1, %rdi
  lea html_inline2(%rip), %rsi
  mov $html_inline2_len, %rdx
  syscall

  mov $60, %rax
  mov $0, %rdi
  syscall
