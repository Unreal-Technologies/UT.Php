<?php
namespace UT_Php\Drawing;

require_once __DIR__.'/../IO/File.php';

abstract class Image extends \UT_Php\IO\File
{
    /**
     * @var \GdImage|null
     */
    private $_image = null;
    
    /**
     * @var Point2D|null
     */
    private $_size = null;
    
    /**
     * @var int|null
     */
    private $_bits = null;
    
    /**
     * @var string|null
     */
    private $_mime = null;
    
    /**
     * @param  \UT_Php\IO\File $file
     * @return Image|null
     * @throws \Exception
     */
    public static function GetImage(\UT_Php\IO\File $file): ?Image
    {
        $ext = strtolower($file -> Extension());
        switch($ext)
        {
        case 'bmp':
            return new Image\Bmp($file -> Path());
        case 'png':
            return new Image\Png($file -> Path());
        default:
            throw new \Exception('Extension "'.$ext.'" is not implemented');
        }
        return null;
    }
    
    abstract function ImageCreate(): mixed;
    abstract function ImageSave(\GdImage $image): bool;
    
    /**
     * @param  Rectangle $rectangle
     * @param  Color     $fillColor
     * @param  Color     $borderColor
     * @return void
     */
    public function GD_Draw_Rectangle(Rectangle $rectangle, Color $fillColor, Color $borderColor = null): void
    {
        $w = $rectangle -> Size() -> X();
        $h = $rectangle -> Size() -> Y();
        $angle = $rectangle -> Rotation();
        
        if(round($angle / 90) % 2 === 1) //Flip W, H
        {
            $t = $w;
            $w = $h;
            $h = $t;
        }
        
        $x1 = $rectangle -> Location() -> X();
        $x2 = $x1 + $w;
        
        $y1 = $rectangle -> Location() -> Y();
        $y2 = $y1 + $h;

        $fc = imagecolorallocatealpha($this -> _image, $fillColor -> R(), $fillColor -> G(), $fillColor -> B(), $fillColor -> A());
        imagefilledrectangle($this -> _image, $x1, $y1, $x2, $y2, $fc);
        
        if($borderColor !== null) {
            $bc = imagecolorallocatealpha($this -> _image, $borderColor -> R(), $borderColor -> G(), $borderColor -> B(), $borderColor -> A());
            imagerectangle($this -> _image, $x1, $y1, $x2, $y2, $bc);
        }
    }
    
    /**
     * @param  Rectangle $rectangle
     * @param  Color     $fillColor
     * @param  Color     $borderColor
     * @return void
     */
    public function GD_Draw_Ellipse(Rectangle $rectangle, Color $fillColor, Color $borderColor = null): void
    {
        $w = $rectangle -> Size() -> X();
        $h = $rectangle -> Size() -> Y();
        $angle = $rectangle -> Rotation();
        
        if(round($angle / 90) % 2 === 1) //Flip W, H
        {
            $t = $w;
            $w = $h;
            $h = $t;
        }
        
        $x = $rectangle -> Location() -> X();
        $y = $rectangle -> Location() -> Y();

        $fc = imagecolorallocatealpha($this -> _image, $fillColor -> R(), $fillColor -> G(), $fillColor -> B(), $fillColor -> A());
        imagefilledellipse($this -> _image, $x, $y, $w, $h, $fc);
        
        if($borderColor !== null) {
            $bc = imagecolorallocatealpha($this -> _image, $borderColor -> R(), $borderColor -> G(), $borderColor -> B(), $borderColor -> A());
            imageellipse($this -> _image, $x, $y, $w, $h, $bc);
        }
    }
    
    /**
     * @return Point2D|null
     */
    public function Size(): ?Point2D
    {
        return $this -> _size;
    }
    
    /**
     * @return bool
     */
    public function GD_Open(): bool
    {
        if($this -> _image !== null) {
            return false;
        }
        
        $imgDat = getimagesize($this -> Path());
        $w = $imgDat[0];
        $h = $imgDat[1];
        $mime = $imgDat['mime'];
        $bits = $imgDat['bits'];
        
        $this -> _size = new Point2D($w, $h);
        $this -> _bits = (int)$bits;
        $this -> _mime = $mime;
        
        $src = $this -> ImageCreate();
        if($src === false) {
            return false;
        }
        $dest = imagecreatetruecolor($w, $h);
        imagecopy($dest, $src, 0, 0, 0, 0, $w, $h);
        
        $this -> _image = $dest;
        return true;
    }
    
    /**
     * @param  \UT_Php\IO\File $file
     * @return bool
     */
    public function GD_SaveAs(\UT_Php\IO\File $file): bool
    {
        if($this -> _image === null) {
            return false;
        }
        $newImg = Image::GetImage($file);
        return $newImg -> ImageSave($this -> _image);
    }
}