.section .data
echo0:
  .ascii "Example text\n"
echo0_len = . - echo0
echo1:
  .ascii "No end line"
echo1_len = . - echo1
echo2:
  .ascii " continue here\n"
echo2_len = . - echo2

.section .text
.globl _start
_start:
  mov $1, %rax
  mov $1, %rdi
  lea echo0(%rip), %rsi
  mov $echo0_len, %rdx
  syscall

  mov $1, %rax
  mov $1, %rdi
  lea echo1(%rip), %rsi
  mov $echo1_len, %rdx
  syscall

  mov $1, %rax
  mov $1, %rdi
  lea echo2(%rip), %rsi
  mov $echo2_len, %rdx
  syscall

  mov $60, %rax
  mov $0, %rdi
  syscall
