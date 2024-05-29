<?php

namespace UT_Php_Core\Drawing;

interface IColor
{
    public function r(): int;
    public function g(): int;
    public function b(): int;
    public function a(): int;
}
