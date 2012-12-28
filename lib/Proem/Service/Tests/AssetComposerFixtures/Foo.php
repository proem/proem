<?php

class Foo
{
    protected $var;

    public function __construct($var = null)
    {
        $this->var = $var;
    }

    public function setVar($var)
    {
        $this->var = $var;
    }

    public function getVar()
    {
        return $this->var;
    }
}
