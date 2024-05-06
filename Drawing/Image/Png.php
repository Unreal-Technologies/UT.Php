<?php
namespace UT_Php\Drawing\Image;

class Png extends \UT_Php\Drawing\Image
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
