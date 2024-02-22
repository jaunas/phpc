<?php

// Test 1: Simple text expression
echo "Hello, World!\n";

// Test 2: Mathematical expressions
echo "5 + 3 = " . 5 + 3 . "\n";
echo "5 - 3 = " . 5 - 3 . "\n";
echo "5 * 3 = " . 5 * 3 . "\n";
echo "5 / 3 = " . 5 / 3 . "\n";

// Test 3: Parenthesis
echo "(5 + 3) =" . (5 + 3) . "\n";
echo "(5 - 3) =" . (5 - 3) . "\n";
echo "(5 * 3) =" . (5 * 3) . "\n";
echo "(5 / 3) =" . (5 / 3) . "\n";
echo "(3 - 4 + 5) =" . (3 - 4 + 5) . "\n";
echo "(3 - 4) + 5 =" . (3 - 4) + 5 . "\n";
echo "3 - (4 + 5) =" . 3 - (4 + 5) . "\n";

// Test 4: Ternary operator
echo "true ? 'true' : 'false' = " . (true ? 'true' : 'false') . "\n";
echo "false ? 'true' : 'false' = " . (false ? 'true' : 'false') . "\n";
echo "true ? 5 : 3 = " . (true ? 5 : 3) . "\n";
echo "false ? 5 : 3 = " . (false ? 5 : 3) . "\n";

// Test 4: Logical expression
echo "Is 5 equal to 3? " . (5 == 3 ? "Yes" : "No") . "\n";
echo "Is 5 not equal to 3? " . (5 != 3 ? "Yes" : "No") . "\n";
echo "Is 5 greater than 3? " . (5 > 3 ? "Yes" : "No") . "\n";
echo "Is 5 greater than or equal to 3? " . (5 >= 3 ? "Yes" : "No") . "\n";
echo "Is 5 less than 3? " . (5 < 3 ? "Yes" : "No") . "\n";
echo "Is 5 less than or equal to 3? " . (5 <= 3 ? "Yes" : "No") . "\n";

//// Test 5: Bitwise expression
//echo "Result of bitwise AND operation: " . (5 & 3) . "\n";
//echo "Result of bitwise OR operation: " . (5 | 3) . "\n";
//
//// Test 6: String concatenation expression
//echo "String 1 " . "String 2 " . "String 3\n";
//
//// Test 7: Additional logical and mathematical operators
//echo "Is true or false: " . (true || false ? "Yes" : "No") . "\n";
//echo "Is true and false: " . (true && false ? "Yes" : "No") . "\n";
//
//// Test 8: Boolean constants
//echo "True constant: " . true . "\n";
//echo "False constant: " . false . "\n";
