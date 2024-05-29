<?php

namespace UT_Php_Core\IO\Common;

class Bmp extends \UT_Php_Core\Drawing\Image implements IBmpFile
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
