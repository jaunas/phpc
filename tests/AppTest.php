<?php

namespace Jaunas\PhpCompiler\Tests;

use Jaunas\PhpCompiler\App;
use Jaunas\PhpCompiler\Exception\FileNotReadable;
use Jaunas\PhpCompiler\Node\Expr\BinaryOp as RustBinaryOp;
use Jaunas\PhpCompiler\Node\Expr\Number as RustNumber;
use Jaunas\PhpCompiler\Node\Expr\String_ as RustString;
use Jaunas\PhpCompiler\Node\Fn_ as RustFn;
use Jaunas\PhpCompiler\Node\MacroCall as RustMacroCall;
use Jaunas\PhpCompiler\Translator;
use Jaunas\PhpCompiler\Visitor\Echo_ as EchoVisitor;
use Jaunas\PhpCompiler\Visitor\InlineHtml as InlineHtmlVisitor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(App::class)]
#[UsesClass(RustFn::class)]
#[UsesClass(RustString::class)]
#[UsesClass(RustMacroCall::class)]
#[UsesClass(RustNumber::class)]
#[UsesClass(Translator::class)]
#[UsesClass(EchoVisitor::class)]
#[UsesClass(InlineHtmlVisitor::class)]
#[UsesClass(RustBinaryOp::class)]
class AppTest extends TestCase
{
    use ScriptNameProvider;

    private function getFixturePath(string $filename): string
    {
        return sprintf('%s/fixtures/%s', __DIR__, $filename);
    }

    #[Test]
    public function translationFailsWhenNoFile(): void
    {
        $app = new App([1 => $this->getFixturePath('not_exists.php')]);

        $this->expectException(FileNotReadable::class);
        $app->generateTranslatedScript();
    }

    #[Test]
    public function generatesTranslatedScript(): void
    {
        $app = new App([1 => $this->getFixturePath('empty.php')]);
        try {
            $app->generateTranslatedScript();
        } catch (FileNotReadable) {
            $this->fail('File not found');
        }

        $this->assertFileEquals(
            $this->getFixturePath('empty.expected.rs'),
            $this->getFixturePath('empty.rs')
        );

        unlink($this->getFixturePath('empty.rs'));
    }

    #[Test]
    #[DataProvider('scriptNameProvider')]
    public function translatedOutputsMatch(string $scriptName): void
    {
        $phpScriptPath = $this->getScriptPath($scriptName, 'php');
        $rustScriptPath = $this->getScriptPath($scriptName, 'rs');
        $execPath = $this->getScriptPath($scriptName);

        $app = new App([1 => $phpScriptPath]);
        try {
            $app->generateTranslatedScript();
        } catch (FileNotReadable $exception) {
            $this->fail(sprintf("Failed to translate the script: %s", $exception->getMessage()));
        }

        exec(sprintf('rustc %s -o %s', $rustScriptPath, $execPath));
        unlink($rustScriptPath);

        $expectedOutput = '';
        exec(sprintf('php %s', $phpScriptPath), $expectedOutput);

        $output = '';
        exec($execPath, $output);

        unlink($execPath);

        $this->assertEquals(implode("\n", $expectedOutput), implode("\n", $output));
    }
}
