<?php

namespace UT_Php_Core\Drawing;

class Color implements IColor
{
    /**
     * @var int
     */
    private int $r;

    /**
     * @var int
     */
    private int $g;

    /**
     * @var int
     */
    private int $b;

    /**
     * @var int
     */
    private int $a;

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
        return $this -> r;
    }

    /**
     * @return int
     */
    public function g(): int
    {
        return $this -> g;
    }

    /**
     * @return int
     */
    public function b(): int
    {
        return $this -> b;
    }

    /**
     * @return int
     */
    public function a(): int
    {
        return $this -> a;
    }

    /**
     * @param int $r
     * @param int $g
     * @param int $b
     * @param int $a
     */
    private function __construct(int $r, int $g, int $b, int $a)
    {
        $this -> r = min([max([$r, 0]), 255]);
        $this -> g = min([max([$g, 0]), 255]);
        $this -> b = min([max([$b, 0]), 255]);
        $this -> a = min([max([$a, 0]), 127]);
    }
}
