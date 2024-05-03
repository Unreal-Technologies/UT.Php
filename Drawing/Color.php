<?php
namespace UT_Php\Drawing;

class Color
{
    /**
     * @var int
     */
    private $_r;
    
    /**
     * @var int
     */
    private $_g;
    
    /**
     * @var int
     */
    private $_b;
    
    /**
     * @var int
     */
    private $_a;
    
    /**
     * @param int $r
     * @param int $g
     * @param int $b
     * @return Color
     */
    public static function FromRGB(int $r, int $g, int $b): Color
    {
        return new Color($r, $g, $b, 0);
    }
    
    /**
     * @param int $r
     * @param int $g
     * @param int $b
     * @param int $a
     * @return Color
     */
    public static function FromRGBA(int $r, int $g, int $b, int $a): Color
    {
        return new Color($r, $g, $b, $a);
    }
    
    /**
     * @return int
     */
    public function R(): int
    {
        return $this -> _r;
    }
    
    /**
     * @return int
     */
    public function G(): int
    {
        return $this -> _g;
    }
    
    /**
     * @return int
     */
    public function B(): int
    {
        return $this -> _b;
    }
    
    /**
     * @return int
     */
    public function A(): int
    {
        return $this -> _a;
    }
    
    /**
     * @param int $r
     * @param int $g
     * @param int $b
     * @param int $a
     */
    private function __construct(int $r, int $g, int $b, int $a)
    {
        $this -> _r = min([max([$r, 0]), 255]);
        $this -> _g = min([max([$g, 0]), 255]);
        $this -> _b = min([max([$b, 0]), 255]);
        $this -> _a = min([max([$a, 0]), 127]);
    }
}