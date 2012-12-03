<?php

require_once 'lib/Proem/Util/Autoloader.php';

( new Proem\Util\AutoLoader())
    ->attachNamespace('Proem\Tests', 'tests/lib')
    ->register();
