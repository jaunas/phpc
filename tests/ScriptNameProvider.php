<?php

namespace Jaunas\PhpCompiler\Tests;

trait ScriptNameProvider
{
    /**
     * @return array<string, string[]>
     */
    public static function scriptNameProvider(): array
    {
        $scriptNames = [];
        foreach (self::getPhpFilenames() as $filename) {
            $scriptName = self::removeExtension($filename);
            $scriptNames[$scriptName] = [$scriptName];
        }

        return $scriptNames;
    }

    /**
     * @return string[]
     */
    private static function getPhpFilenames(): array
    {
        if (false === $files = scandir(__DIR__ . '/fixtures/')) {
            static::fail("Couldn't scan the directory");
        }

        return array_filter($files, static function ($filename) {
            return preg_match('/\\.php$/', $filename) === 1;
        });
    }

    private static function removeExtension(string $filename): string
    {
        $dotPos = strrpos($filename, ".");
        return $dotPos ? substr($filename, 0, $dotPos) : $filename;
    }

    protected function getScriptPath(string $scriptName, ?string $extension = null): string
    {
        $extension = $extension ? '.' . $extension : '';
        return sprintf("%s/fixtures/%s%s", __DIR__, $scriptName, $extension);
    }
}
