<?php

namespace Jaunas\PhpCompiler\Tests;

trait ScriptNameProvider
{
    public static function scriptNameProvider(): array
    {
        $scriptNames = [];
        foreach (self::getPhpFilenames() as $filename) {
            $scriptName = self::removeExtension($filename);
            $scriptNames[$scriptName] = [$scriptName];
        }
        return $scriptNames;
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

    protected function getScriptPath(string $scriptName, ?string $extension = null): string
    {
        $extension = $extension ? '.' . $extension : '';
        return sprintf("%s/fixtures/%s%s", __DIR__, $scriptName, $extension);
    }
}