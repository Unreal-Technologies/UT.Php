<?php

namespace UT_Php\Drawing;

class Rectangle
{
    /**
     * @var Point2D
     */
    private $size_;

    /**
     * @var Point2D
     */
    private $location_;

    /**
     * @var int
     */
    private $rotation_;

    /**
     * @param Point2D $size
     * @param Point2D $location
     * @param int     $rotation
     */
    public function __construct(Point2D $size, Point2D $location, int $rotation = 0)
    {
        $this -> size_ = $size;
        $this -> location_ = $location;
        $this -> rotation_ = $rotation;
    }

    /**
     * @return Point2D
     */
    public function location(): Point2D
    {
        return $this -> location_;
    }

    /**
     * @return Point2D
     */
    public function size(): Point2D
    {
        return $this -> size_;
    }

    /**
     * @return int
     */
    public function rotation(): int
    {
        return $this -> rotation_;
    }
}
