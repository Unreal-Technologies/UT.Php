<?php
namespace UT_Php\Drawing;

class Rectangle
{
    /**
     * @var Point2D
     */
    private $_size;
    
    /**
     * @var Point2D
     */
    private $_location;
    
    /**
     * @var int
     */
    private $_rotation;
    
    /**
     * @param Point2D $size
     * @param Point2D $location
     * @param int $rotation
     */
    public function __construct(Point2D $size, Point2D $location, int $rotation = 0) 
    {
        $this -> _size = $size;
        $this -> _location = $location;
        $this -> _rotation = $rotation;
    }
    
    /**
     * @return Point2D
     */
    public function Location(): Point2D
    {
        return $this -> _location;
    }
    
    /**
     * @return Point2D
     */
    public function Size(): Point2D
    {
        return $this -> _size;
    }
    
    /**
     * @return int
     */
    public function Rotation(): int
    {
        return $this -> _rotation;
    }
}