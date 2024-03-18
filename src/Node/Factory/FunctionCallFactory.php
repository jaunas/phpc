<?php

namespace Jaunas\PhpCompiler\Node\Factory;

use Jaunas\PhpCompiler\Exception\FunctionNotFound;
use Jaunas\PhpCompiler\Node\Expr\BinaryOp;
use Jaunas\PhpCompiler\Node\Expr\Expr;
use Jaunas\PhpCompiler\Node\Expr\FnCall;
use Jaunas\PhpCompiler\Node\Expr\FunctionCall;
use Jaunas\PhpCompiler\Node\Expr\If_;
use Jaunas\PhpCompiler\Node\Expr\Value\Bool_;
use Jaunas\PhpCompiler\Node\Expr\Value\Null_;
use Jaunas\PhpCompiler\Node\Expr\Value\Number;
use Jaunas\PhpCompiler\Node\Expr\Value\String_;
use Jaunas\PhpCompiler\Node\Expr\Value\Value;
use PhpParser\Node\Expr as PhpExpr;
use PhpParser\Node\Expr\BinaryOp as PhpBinaryOp;
use PhpParser\Node\Expr\BinaryOp\Concat as PhpConcat;
use PhpParser\Node\Expr\ConstFetch as PhpConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Ternary as PhpTernary;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Float_ as PhpFloat;
use PhpParser\Node\Scalar\Int_ as PhpInt;
use PhpParser\Node\Scalar\String_ as PhpString;

class FunctionCallFactory
{
    private const NAME_MAP = [
        'echo' => 'Echo',
        'var_dump' => 'VarDump',
        'file_exists' => 'FileExists',
    ];

    private function __construct()
    {
    }

    public static function fromFuncCall(FuncCall $funcCall): FunctionCall
    {
        return new FunctionCall(self::getFunctionName($funcCall), self::getArgs($funcCall));
    }

    private static function getFunctionName(FuncCall $funcCall): string
    {
        if (!$funcCall->name instanceof Name) {
            throw new FunctionNotFound();
        }

        $phpName = (string)$funcCall->name;
        if (!isset(self::NAME_MAP[$phpName])) {
            throw new FunctionNotFound();
        }

        return self::NAME_MAP[$phpName];
    }

    /**
     * @return Expr[]
     */
    private static function getArgs(FuncCall $funcCall): array
    {
        $args = [];
        foreach ($funcCall->getArgs() as $arg) {
            $expr = self::getExpr($arg->value);
            if (!$expr instanceof Expr) {
                continue;
            }

            $args[] = $expr;
        }

        return $args;
    }

    private static function getExpr(?PhpExpr $node): ?Expr
    {
        if ($node instanceof PhpConcat) {
            return self::getConcatExpr($node);
        }

        if ($node instanceof PhpTernary) {
            return self::getIfExpr($node);
        }

        if ($node instanceof PhpString) {
            return String_::fromString($node->value);
        }

        if ($node instanceof PhpInt || $node instanceof PhpFloat) {
            return new Number($node->value);
        }

        if ($node instanceof PhpBinaryOp) {
            return self::getBinaryOpExpr($node);
        }

        if ($node instanceof PhpConstFetch) {
            return self::getConstValue($node);
        }

        return null;
    }

    private static function getConcatExpr(PhpConcat $node): ?FnCall
    {
        $left = self::getExpr($node->left);
        $right = self::getExpr($node->right);

        if (!$left instanceof Expr || !$right instanceof Expr) {
            return null;
        }

        $fnCall = new FnCall('concat', [$right]);
        $fnCall->setSubject($left);

        return $fnCall;
    }

    private static function getIfExpr(PhpTernary $node): ?If_
    {
        $condition = self::getExpr($node->cond);
        $then = self::getExpr($node->if);
        $else = self::getExpr($node->else);

        if (!$condition instanceof Expr || !$then instanceof Expr || !$else instanceof Expr) {
            return null;
        }

        return new If_($condition, $then, $else);
    }

    private static function getBinaryOpExpr(PhpBinaryOp $node): ?BinaryOp
    {
        $left = self::getExpr($node->left);
        $right = self::getExpr($node->right);
        if ($left instanceof Expr && $right instanceof Expr) {
            return new BinaryOp($node->getOperatorSigil(), $left, $right);
        }

        return null;
    }

    private static function getConstValue(PhpConstFetch $node): ?Value
    {
        $name = $node->name->name;
        if ($name == 'true') {
            return new Bool_(true);
        }

        if ($name == 'false') {
            return new Bool_(false);
        }

        if ($name == 'null') {
            return new Null_();
        }

        return null;
    }
}
