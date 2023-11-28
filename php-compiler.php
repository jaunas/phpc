<?php

use Jaunas\PhpCompiler\App;

require_once __DIR__ . '/vendor/autoload.php';

(new App($argv))->generateTranslatedScript();
