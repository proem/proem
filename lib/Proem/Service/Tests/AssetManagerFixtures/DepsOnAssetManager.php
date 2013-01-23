<?php

use \Proem\Service\AssetManager;

class DepsOnAssetManager
{
    protected $am;

    public function __construct(AssetManager $am)
    {
        $this-> am = $am;
    }

    public function getAssetManager()
    {
        return $this->am;
    }
}
