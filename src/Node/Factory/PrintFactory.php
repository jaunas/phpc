<?php

namespace Jaunas\PhpCompiler\Node\Factory;

use Jaunas\PhpCompiler\Node\Expr\BinaryOp;
use Jaunas\PhpCompiler\Node\Expr\Number;
use Jaunas\PhpCompiler\Node\Expr\PhpNumber;
use Jaunas\PhpCompiler\Node\Expr\String_;
use Jaunas\PhpCompiler\Node\MacroCall;

class PrintFactory
{
    public static function createWithString(string $string): MacroCall
    {
        return new MacroCall('print', new String_($string));
    }


    public static function createWithNumber(int|BinaryOp $number): MacroCall
    {
        if (is_numeric($number)) {
            $number = new Number($number);
        }

        return new MacroCall('print', new String_('{}'), new PhpNumber($number));
    }
}
