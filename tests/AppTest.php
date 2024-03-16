<?php

namespace Jaunas\PhpCompiler\Tests;

use Jaunas\PhpCompiler\App;
use Jaunas\PhpCompiler\Exception\FileNotReadable;
use Jaunas\PhpCompiler\Node\Expr\BinaryOp as RustBinaryOp;
use Jaunas\PhpCompiler\Node\Expr\FnCall;
use Jaunas\PhpCompiler\Node\Expr\If_ as RustIf;
use Jaunas\PhpCompiler\Node\Expr\MacroCall as RustMacroCall;
use Jaunas\PhpCompiler\Node\Expr\StrRef;
use Jaunas\PhpCompiler\Node\Expr\Value\Bool_;
use Jaunas\PhpCompiler\Node\Expr\Value\Number;
use Jaunas\PhpCompiler\Node\Expr\Value\String_;
use Jaunas\PhpCompiler\Node\Factory\PrintFactory;
use Jaunas\PhpCompiler\Node\Fn_ as RustFn;
use Jaunas\PhpCompiler\Translator;
use Jaunas\PhpCompiler\Visitor\Echo_ as EchoVisitor;
use Jaunas\PhpCompiler\Visitor\InlineHtml as InlineHtmlVisitor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(App::class)]
#[UsesClass(RustBinaryOp::class)]
#[UsesClass(RustIf::class)]
#[UsesClass(StrRef::class)]
#[UsesClass(PrintFactory::class)]
#[UsesClass(RustFn::class)]
#[UsesClass(RustMacroCall::class)]
#[UsesClass(Translator::class)]
#[UsesClass(EchoVisitor::class)]
#[UsesClass(InlineHtmlVisitor::class)]
#[UsesClass(String_::class)]
#[UsesClass(Number::class)]
#[UsesClass(Bool_::class)]
#[UsesClass(FnCall::class)]
class AppTest extends TestCase
{
    use ScriptNameProvider;

    #[Test]
    public function translationFailsWhenNoFile(): void
    {
        $app = new App([1 => $this->getScriptPath('not_exists', 'php')]);

        $this->expectException(FileNotReadable::class);
        $app->generateTranslatedScript();
    }

    #[Test]
    public function generatesTranslatedScript(): void
    {
        $this->generateTranslatedScript('empty');

        $rustScriptPath = $this->getScriptPath('empty', 'rs');
        $expected = "use rust_php::*;\nuse rust_php::functions::Function;\nfn main() {\n}\n";
        $this->assertStringEqualsFile($rustScriptPath, $expected);

        unlink($rustScriptPath);
    }

    #[Test]
    #[DataProvider('scriptNameProvider')]
    public function translatedOutputsMatch(string $scriptName): void
    {
        $this->generateTranslatedScript($scriptName);

        $rustResult = $this->fetchRustResult($scriptName);
        $phpResult = $this->fetchPhpResult($scriptName);

        $this->assertEquals($phpResult, $rustResult);
    }

    private function generateTranslatedScript(string $scriptName): void
    {
        $phpScriptPath = $this->getScriptPath($scriptName, 'php');
        $app = new App([1 => $phpScriptPath]);

        try {
            $app->generateTranslatedScript();
        } catch (FileNotReadable $fileNotReadable) {
            $this->fail(sprintf("Failed to translate the script: %s", $fileNotReadable->getMessage()));
        }
    }

    private function fetchRustResult(string $scriptName): ProcessResult
    {
        $scriptPath = $this->getScriptPath($scriptName, 'rs');
        $result = new ProcessResult(['cargo', 'run', '-q', '--example', $scriptName], __DIR__ . '/../rust-php', ['RUSTFLAGS' => '-Awarnings']);
        unlink($scriptPath);

        return $result;
    }

    private function fetchPhpResult(string $scriptName): ProcessResult
    {
        $scriptPath = $this->getScriptPath($scriptName, 'php');
        return new ProcessResult(['php', $scriptPath], null);
    }
}
