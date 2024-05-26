<?php

namespace UT_Php_Core\Drawing;

class Rectangle implements IRectangle
{
    /**
     * @var ISize2D
     */
    private ISize2D $size;

    /**
     * @var IPoint2D
     */
    private IPoint2D $location;

    /**
     * @var int
     */
    private int $rotation;

    /**
     * @param IPoint2D $size
     * @param ISize2D $location
     * @param int $rotation
     */
    public function __construct(
        IPoint2D $size,
        ISize2D $location,
        int $rotation = 0
    ) {
        $this -> size = $size;
        $this -> location = $location;
        $this -> rotation = $rotation;
    }

    /**
     * @return IPoint2D
     */
    public function location(): IPoint2D
    {
        return $this -> location;
    }

    /**
     * @return ISize2D
     */
    public function size(): ISize2D
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
