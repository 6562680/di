<?php

namespace Gzhegow\Di\Demo;

class MyClassFour
{
    /**
     * @var MyClassOneInterface
     */
    public $one;


    public function __autowire(MyClassOneInterface $one)
    {
        $this->one = $one;
    }
}
