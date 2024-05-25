<?php

namespace UT_Php_Core\Interfaces;

interface IRectangle
{
    public function location(): IPoint2D;
    public function size(): ISize2D;
    public function rotation(): int;
}
