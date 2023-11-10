.section .data
html_inline0:
  .ascii "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed quis tincidunt eros. Donec posuere nibh a molestie malesuada. Nulla fringilla efficitur neque ac volutpat. Praesent faucibus eleifend accumsan. Donec aliquam nisl ut pulvinar consequat. Morbi non elit a augue cursus egestas. Vivamus turpis nisi, condimentum vel massa vitae, fringilla aliquam tortor. Nullam ac augue iaculis, euismod tortor at, volutpat lacus. Aliquam placerat ultricies purus, vitae tempus justo convallis et.\n"
  .ascii "In ac elementum nunc. Phasellus et dapibus felis. Vivamus molestie ipsum at tortor iaculis tempus. Aliquam nulla magna, consectetur quis justo ac, consectetur gravida nisi. In mattis eget leo in ullamcorper. Duis porta pellentesque leo, eu suscipit nibh cursus eget. Fusce sed condimentum magna.\n"
  .ascii "Vestibulum vulputate turpis eu scelerisque lacinia. Aliquam volutpat dui sit amet felis viverra, quis lacinia mauris venenatis. In ut ullamcorper urna. Praesent vitae est purus. Fusce quis ex venenatis, tristique magna vel, ornare risus. Praesent sed felis eleifend, luctus lorem ac, fringilla nunc. Integer ullamcorper, ex eget euismod iaculis, est nisi feugiat arcu, quis gravida dui arcu eu velit. Nullam eu tortor vel ligula suscipit tristique.\n"
  .ascii "\n"
  .ascii "Pellentesque odio lorem, semper in nulla at, tempus interdum turpis. Cras ultricies interdum aliquet. Donec elementum elementum arcu, in tempus elit.\n"
html_inline0_len = . - html_inline0

.section .text
.globl _start
_start:
  mov $1, %rax
  mov $1, %rdi
  lea html_inline0(%rip), %rsi
  mov $html_inline0_len, %rdx
  syscall

  mov $60, %rax
  mov $0, %rdi
  syscall
