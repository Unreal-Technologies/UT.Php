<?php

namespace UT_Php\Drawing;

class Size2D implements \UT_Php\Interfaces\ISize2D
{
    /**
     * @var float
     */
    private $w;

    /**
     * @var float
     */
    private $h;

    /**
     * @param float $w
     * @param float $h
     */
    public function __construct(float $w, float $h)
    {
        $this -> w = $w;
        $this -> h = $h;
    }

    /**
     * @return float
     */
    public function w(): float
    {
        return $this -> w;
    }

    /**
     * @return float
     */
    public function h(): float
    {
        return $this -> h;
    }
}
