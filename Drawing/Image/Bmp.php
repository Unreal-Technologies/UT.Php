<?php
namespace UT_Php\Drawing\Image;

require_once(__DIR__.'/../Image.php');

class Bmp extends \UT_Php\Drawing\Image
{
    /**
     * @return mixed
     */
    public function ImageCreate(): mixed 
    {
        return imagecreatefrombmp($this -> Path());
    }
    
    /**
     * @param \GdImage $image
     * @return bool
     */
    public function ImageSave(\GdImage $image): bool 
    {
        return imagebmp($image, $this -> Path());
    }
}