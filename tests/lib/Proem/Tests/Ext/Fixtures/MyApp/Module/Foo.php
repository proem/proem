<?php

namespace MyApp\Module;

use \Proem\Service\Manager;

class Foo extends \Proem\Ext\Module\Generic
{
    public function init(Manager $assets, $env = null) {
        $assets->get('events')->attach([
            'name'      => 'pre.in.route',
            'callback'  => [$this, 'loadRoutes']
        ]);
    }

    public function loadRoutes($e) {
        echo 'Foo Module Loaded';
    }
}
