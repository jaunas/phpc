<?php

namespace Jaunas\PhpCompiler;

use Jaunas\PhpCompiler\Node\Fn_;
use Jaunas\PhpCompiler\Node\Mod;
use Jaunas\PhpCompiler\Node\Use_;
use Jaunas\PhpCompiler\Visitor\Echo_;
use Jaunas\PhpCompiler\Visitor\FuncCall;
use Jaunas\PhpCompiler\Visitor\InlineHtml;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;

class Translator
{
    private Fn_ $main;

    private NodeTraverser $traverser;

    public function __construct()
    {
        $this->main = new Fn_('main');

        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor(new Echo_($this->main));
        $this->traverser->addVisitor(new FuncCall($this->main));
        $this->traverser->addVisitor(new InlineHtml($this->main));
    }

    /**
     * @param Stmt[] $array
     */
    public function translate(array $array): Mod
    {
        $this->traverser->traverse($array);

        $mainMod = new Mod();
        $mainMod->addStatement(new Use_(['rust_php', '*']));
        $mainMod->addStatement(new Use_(['rust_php', 'functions', 'Function']));
        $mainMod->addStatement($this->main);

        return $mainMod;
    }
}
