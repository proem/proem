<?php

class Foo
{
    protected $bar;
    protected $what;

    public function __construct(Bar $bar, $what = 'thisiswhat')
    {
        $this->bar = $bar;
        $this->what = $what;
    }

    public function getBar()
    {
        return $this->bar;
    }

    public function getWhat()
    {
        return $this->what;
    }
}
