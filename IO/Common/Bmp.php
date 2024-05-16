<?php

namespace UT_Php\IO\Common;

class Bmp extends \UT_Php\Drawing\Image implements \UT_Php\Interfaces\IBmpFile
{
    /**
     * @return mixed
     */
    public function imageCreate(): mixed
    {
        return imagecreatefrombmp($this -> path());
    }

    /**
     * @param  \GdImage $image
     * @return bool
     */
    public function imageSave(\GdImage $image): bool
    {
        return imagebmp($image, $this -> path());
    }
}
