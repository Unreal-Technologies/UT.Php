<?php
namespace UT_Php\Drawing\Image;

class Bmp extends \UT_Php\Drawing\Image
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
