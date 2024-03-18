<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Concat\JoinStringConcatRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/../src',
        __DIR__ . '/',
    ]);

    // define sets of rules
    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
    ]);

    $rectorConfig->skip([
        JoinStringConcatRector::class => [
            __DIR__ . '/fixtures/concat.php',
            __DIR__ . '/fixtures/echo_expr.php',
            __DIR__ . '/fixtures/expr.php',
        ]
    ]);
};
