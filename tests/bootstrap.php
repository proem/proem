<?php

require_once 'lib/Proem/Util/Autoloader.php';

use Proem\Util\Autoloader;

(new AutoLoader())
    ->attachNamespace('Proem\Tests', 'tests/lib')
    ->register();
