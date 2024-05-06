<?php

namespace UT_Php\Drawing;

class Point2D
{
    /**
     * @var float
     */
    private $x_;

    /**
     * @var float
     */
    private $y_;

    /**
     * @param float $x
     * @param float $y
     */
    public function __construct(float $x, float $y)
    {
        $this -> x_ = $x;
        $this -> y_ = $y;
    }

    /**
     * @return float
     */
    public function x(): float
    {
        return $this -> x_;
    }

    /**
     * @return float
     */
    public function y(): float
    {
        return $this -> y_;
    }
}
