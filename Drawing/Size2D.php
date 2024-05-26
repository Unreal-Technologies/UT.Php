<?php

namespace UT_Php_Core\Drawing;

class Size2D implements ISize2D
{
    /**
     * @var float
     */
    private float $w;

    /**
     * @var float
     */
    private float $h;

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
