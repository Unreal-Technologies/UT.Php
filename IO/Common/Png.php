<?php

namespace UT_Php_Core\IO\Common;

class Png extends \UT_Php_Core\Drawing\Image implements \UT_Php_Core\Interfaces\IPngFile
{
    /**
     * @return mixed
     */
    public function imageCreate(): mixed
    {
        return imagecreatefrompng($this -> path());
    }

    /**
     * @param  \GdImage $image
     * @return bool
     */
    public function imageSave(\GdImage $image): bool
    {
        return imagepng($image, $this -> path());
    }
}
