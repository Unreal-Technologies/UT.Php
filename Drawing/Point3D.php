<?php
namespace UT_Php\Drawing;

class Point3D
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
     * @var float
     */
    private $_z;
    
    /**
     * @param float $x
     * @param float $y
     * @param float $z
     */
    function __construct(float $x, float $y, float $z)
    {
        $this -> _x = $x;
        $this -> _y = $y;
        $this -> _z = $z;
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
    
    /**
     * @return float
     */
    public function Z(): float
    {
        return $this -> _z;
    }
}