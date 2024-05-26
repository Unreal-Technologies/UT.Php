<?php

namespace UT_Php_Core\Drawing;

class Point3D extends Point2D implements IPoint3D
{
    /**
     * @var float
     */
    private float $z;

    /**
     * @param float $x
     * @param float $y
     * @param float $z
     */
    public function __construct(float $x, float $y, float $z)
    {
        parent::__construct($x, $y);
        $this -> z = $z;
    }

    /**
     * @return float
     */
    public function z(): float
    {
        return $this -> z;
    }
}
