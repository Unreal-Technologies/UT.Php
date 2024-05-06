<?php
namespace UT_Php\Drawing\Image;

require_once __DIR__.'/../Image.php';

class Png extends \UT_Php\Drawing\Image
{
    /**
     * @return mixed
     */
    public function ImageCreate(): mixed 
    {
        return imagecreatefrompng($this -> Path());
    }
    
    /** 
     * @param  \GdImage $image
     * @return bool
     */
    public function ImageSave(\GdImage $image): bool 
    {
        return imagepng($image, $this -> Path());
    }
}