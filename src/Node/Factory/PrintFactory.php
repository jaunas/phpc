<?php

namespace Jaunas\PhpCompiler\Node\Factory;

use Jaunas\PhpCompiler\Node\Expr\ArithmeticExpr;
use Jaunas\PhpCompiler\Node\Expr\Expr;
use Jaunas\PhpCompiler\Node\Expr\Number;
use Jaunas\PhpCompiler\Node\Expr\PhpNumber;
use Jaunas\PhpCompiler\Node\Expr\StrRef;
use Jaunas\PhpCompiler\Node\Expr\Value\Null_;
use Jaunas\PhpCompiler\Node\Expr\Value\String_;
use Jaunas\PhpCompiler\Node\MacroCall;

class PrintFactory
{
    public static function createWithNull(): MacroCall
    {
        return new MacroCall('print', StrRef::placeholder(), new Null_());
    }

    public static function createWithString(string $string): MacroCall
    {
        return new MacroCall('print', StrRef::placeholder(), String_::fromString($string));
    }

    public static function createWithNumber(int|ArithmeticExpr $number): MacroCall
    {
        if (is_numeric($number)) {
            $number = new Number($number);
        }

        return new MacroCall('print', StrRef::placeholder(), new PhpNumber($number));
    }

    public static function createWithExpr(Expr $expr): MacroCall
    {
        if ($expr instanceof StrRef) {
            return new MacroCall('print', $expr);
        } else {
            return new MacroCall('print', StrRef::placeholder(), new PhpNumber($expr));
        }
    }
}
