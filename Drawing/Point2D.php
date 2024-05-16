<?php

namespace UT_Php\Drawing;

class Point2D implements \UT_Php\Interfaces\IPoint2D
{
    /**
     * @var float
     */
    private $x;

    /**
     * @var float
     */
    private $y;

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
