<?php

class NeedsInterface
{
    protected $bar;

    public function __construct(SomeInterface $bar)
    {
        $this->bar = $bar;
    }

    public function getBar()
    {
        return $this->bar;
    }
}
