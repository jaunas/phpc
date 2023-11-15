<?php

namespace Jaunas\PhpCompiler\Tests;

trait ScriptNameProvider
{
    public static function scriptNameProvider(): array
    {
        return array_map(function ($filename) {
            return [self::removeExtension($filename)];
        }, self::getPhpFilenames());
    }

    private static function getPhpFilenames(): array
    {
        $files = scandir(__DIR__ . '/fixtures/');
        return array_filter($files, function ($filename) {
            return preg_match('/\\.php$/', $filename);
        });
    }

    private static function removeExtension(mixed $filename): string
    {
        return substr($filename, 0, strrpos($filename, "."));
    }

    protected function getScriptPath(string $scriptName, string $extension): string
    {
        return sprintf("%s/fixtures/%s.%s", __DIR__, $scriptName, $extension);
    }
}