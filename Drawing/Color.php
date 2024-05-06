<?php
namespace UT_Php\Drawing;

class Color
{
    /**
     * @var int
     */
    private $r_;
    
    /**
     * @var int
     */
    private $g_;
    
    /**
     * @var int
     */
    private $b_;
    
    /**
     * @var int
     */
    private $a_;
    
    /**
     * @param  int $r
     * @param  int $g
     * @param  int $b
     * @return Color
     */
    public static function fromRGB(int $r, int $g, int $b): Color
    {
        return new Color($r, $g, $b, 0);
    }
    
    /**
     * @param  int $r
     * @param  int $g
     * @param  int $b
     * @param  int $a
     * @return Color
     */
    public static function fromRGBA(int $r, int $g, int $b, int $a): Color
    {
        return new Color($r, $g, $b, $a);
    }
    
    /**
     * @return int
     */
    public function r(): int
    {
        return $this -> r_;
    }
    
    /**
     * @return int
     */
    public function g(): int
    {
        return $this -> g_;
    }
    
    /**
     * @return int
     */
    public function b(): int
    {
        return $this -> b_;
    }
    
    /**
     * @return int
     */
    public function a(): int
    {
        return $this -> a_;
    }
    
    /**
     * @param int $r
     * @param int $g
     * @param int $b
     * @param int $a
     */
    private function __construct(int $r, int $g, int $b, int $a)
    {
        $this -> r_ = min([max([$r, 0]), 255]);
        $this -> g_ = min([max([$g, 0]), 255]);
        $this -> b_ = min([max([$b, 0]), 255]);
        $this -> a_ = min([max([$a, 0]), 127]);
    }
}
