<?php

namespace UT_Php_Core\Drawing;

class Point2D implements IPoint2D
{
    /**
     * @var float
     */
    private float $x;

    /**
     * @var float
     */
    private float $y;

    /**
     * @param float $x
     * @param float $y
     */
    public function __construct(float $x, float $y)
    {
        $this -> x = $x;
        $this -> y = $y;
    }

    /**
     * @return float
     */
    public function x(): float
    {
        return $this -> x;
    }

    /**
     * @return float
     */
    public function y(): float
    {
        return $this -> y;
    }
}
