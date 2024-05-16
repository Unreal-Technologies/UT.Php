<?php

namespace UT_Php\Drawing;

class Rectangle implements \UT_Php\Interfaces\IRectangle
{
    /**
     * @var \UT_Php\Interfaces\ISize2D
     */
    private $size;

    /**
     * @var \UT_Php\Interfaces\IPoint2D
     */
    private $location;

    /**
     * @var int
     */
    private $rotation;

    /**
     * @param \UT_Php\Interfaces\IPoint2D $size
     * @param \UT_Php\Interfaces\ISize2D $location
     * @param int $rotation
     */
    public function __construct(
        \UT_Php\Interfaces\IPoint2D $size,
        \UT_Php\Interfaces\ISize2D $location,
        int $rotation = 0
    ) {
        $this -> size = $size;
        $this -> location = $location;
        $this -> rotation = $rotation;
    }

    /**
     * @return \UT_Php\Interfaces\IPoint2D
     */
    public function location(): \UT_Php\Interfaces\IPoint2D
    {
        return $this -> location;
    }

    /**
     * @return \UT_Php\Interfaces\ISize2D
     */
    public function size(): \UT_Php\Interfaces\ISize2D
    {
        return $this -> size;
    }

    /**
     * @return int
     */
    public function rotation(): int
    {
        return $this -> rotation;
    }
}
