<?php

namespace Jaunas\PhpCompiler;

use Jaunas\PhpCompiler\Node\Fn_;
use Jaunas\PhpCompiler\Node\MacroCall;
use Jaunas\PhpCompiler\Node\String_;
use PhpParser\Node;
use PhpParser\Node\Stmt\InlineHTML;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class Translator
{
    public function translate(array $array): Fn_
    {
        $main = new Fn_('main');

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new class($main) extends NodeVisitorAbstract {
            public function __construct(private readonly Fn_ $main)
            {
            }

            public function enterNode(Node $node): void
            {
                if ($node instanceof InlineHTML) {
                    $this->main->addToBody(new MacroCall('print', new String_($node->value)));
                }
            }
        });
        $traverser->traverse($array);

        return $main;
    }
}
