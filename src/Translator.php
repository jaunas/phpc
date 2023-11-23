<?php

namespace Jaunas\PhpCompiler;

use Jaunas\PhpCompiler\Node\Fn_;
use Jaunas\PhpCompiler\Visitor\InlineHtml;
use PhpParser\NodeTraverser;

class Translator
{
    private Fn_ $main;
    private NodeTraverser $traverser;

    public function __construct()
    {
        $this->main = new Fn_('main');

        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor(new InlineHtml($this->main));
    }

    public function translate(array $array): Fn_
    {
        $this->traverser->traverse($array);
        return $this->main;
    }
}
