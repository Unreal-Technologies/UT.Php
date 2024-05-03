<?php
namespace UT_Php\Drawing;

class Point2D
{
    /** 
     * @var float
     */
    private $_x;
    
    /** 
     * @var float
     */
    private $_y;
    
    /**
     * @param float $x
     * @param float $y
     */
    function __construct(float $x, float $y)
    {
        $this -> _x = $x;
        $this -> _y = $y;
    }
    
    /**
     * @return float
     */
    public function X(): float
    {
        return $this -> _x;
    }
    
    /**
     * @return float
     */
    public function Y(): float
    {
        return $this -> _y;
    }
}