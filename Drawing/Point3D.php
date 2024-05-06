<?php

namespace UT_Php\Drawing;

class Point3D extends Point2D
{
    /**
     * @var float
     */
    private $z_;

    /**
     * @param float $x
     * @param float $y
     * @param float $z
     */
    public function __construct(float $x, float $y, float $z)
    {
        parent::__construct($x, $y);
        $this -> z_ = $z;
    }

    /**
     * @return float
     */
    public function z(): float
    {
        return $this -> z_;
    }
}
