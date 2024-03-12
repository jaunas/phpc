<?php

namespace Jaunas\PhpCompiler\Node\Factory;

use Jaunas\PhpCompiler\Node\Expr\Expr;
use Jaunas\PhpCompiler\Node\Expr\StrRef;
use Jaunas\PhpCompiler\Node\Expr\Value\Null_;
use Jaunas\PhpCompiler\Node\Expr\Value\Number;
use Jaunas\PhpCompiler\Node\Expr\Value\String_;
use Jaunas\PhpCompiler\Node\Expr\Value\Value;
use Jaunas\PhpCompiler\Node\MacroCall;

class PrintFactory
{
    public static function createWithValue(Value $value): MacroCall
    {
        return new MacroCall('print', StrRef::placeholder(), $value);
    }

    public static function createWithNull(): MacroCall
    {
        return self::createWithValue(new Null_());
    }

    public static function createWithString(string $string): MacroCall
    {
        return self::createWithExpr(new StrRef($string));
    }

    public static function createWithStringValue(string $string): MacroCall
    {
        return self::createWithExpr(String_::fromString($string));
    }

    public static function createWithNumber(int|float $number): MacroCall
    {
        return self::createWithValue(new Number($number));
    }

    public static function createWithExpr(Expr $expr): MacroCall
    {
        if ($expr instanceof StrRef) {
            return new MacroCall('print', $expr);
        } else {
            return new MacroCall('print', StrRef::placeholder(), $expr);
        }
    }
}
