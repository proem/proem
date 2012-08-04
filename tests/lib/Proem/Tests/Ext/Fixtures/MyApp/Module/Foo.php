<?php

namespace MyApp\Module;

use \Proem\Service\Manager\Template as Manager;

class Foo extends \Proem\Ext\Module\Generic
{
    public function init(Manager $assets, $env = null) {
        $assets->get('events')->attach('proem.pre.in.router', [$this, 'loadRoutes']);
    }

    public function loadRoutes($e) {
        echo 'Foo Module Loaded';
    }
}
