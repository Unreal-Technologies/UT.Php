<?php
namespace UT_Php\Drawing;

abstract class Image extends \UT_Php\IO\File
{
    /**
     * @var \GdImage|null
     */
    private $image_ = null;
    
    /**
     * @var Point2D|null
     */
    private $size_ = null;
    
    /**
     * @var int|null
     */
    private $bits_ = null;
    
    /**
     * @var string|null
     */
    private $mime_ = null;
    
    /**
     * @param  \UT_Php\IO\File $file
     * @return Image|null
     * @throws \Exception
     */
    public static function getImage(\UT_Php\IO\File $file): ?Image
    {
        $ext = strtolower($file -> extension());
        switch ($ext) {
            case 'bmp':
                return new Image\Bmp($file -> path());
            case 'png':
                return new Image\Png($file -> path());
            default:
                throw new \Exception('Extension "'.$ext.'" is not implemented');
        }
        return null;
    }
    
    abstract public function imageCreate(): mixed;
    abstract public function imageSave(\GdImage $image): bool;
    
    /**
     * @param  Rectangle $rectangle
     * @param  Color     $fillColor
     * @param  Color     $borderColor
     * @return void
     */
    public function gdDrawRectangle(Rectangle $rectangle, Color $fillColor, Color $borderColor = null): void
    {
        $w = $rectangle -> size() -> x();
        $h = $rectangle -> size() -> y();
        $angle = $rectangle -> rotation();
        
        if (round($angle / 90) % 2 === 1) { //Flip W, H
            $t = $w;
            $w = $h;
            $h = $t;
        }
        
        $x1 = $rectangle -> location() -> x();
        $x2 = $x1 + $w;
        
        $y1 = $rectangle -> location() -> y();
        $y2 = $y1 + $h;

        $fc = imagecolorallocatealpha(
            $this -> image_,
            $fillColor -> r(),
            $fillColor -> g(),
            $fillColor -> b(),
            $fillColor -> a()
        );
        imagefilledrectangle($this -> image_, $x1, $y1, $x2, $y2, $fc);
        
        if ($borderColor !== null) {
            $bc = imagecolorallocatealpha(
                $this -> image_,
                $borderColor -> r(),
                $borderColor -> g(),
                $borderColor -> b(),
                $borderColor -> a()
            );
            imagerectangle($this -> image_, $x1, $y1, $x2, $y2, $bc);
        }
    }
    
    /**
     * @param  Rectangle $rectangle
     * @param  Color     $fillColor
     * @param  Color     $borderColor
     * @return void
     */
    public function gdDrawEllipse(Rectangle $rectangle, Color $fillColor, Color $borderColor = null): void
    {
        $w = $rectangle -> size() -> x();
        $h = $rectangle -> size() -> y();
        $angle = $rectangle -> rotation();
        
        if (round($angle / 90) % 2 === 1) { //Flip W, H
            $t = $w;
            $w = $h;
            $h = $t;
        }
        
        $x = $rectangle -> location() -> x();
        $y = $rectangle -> location() -> y();

        $fc = imagecolorallocatealpha(
            $this -> image_,
            $fillColor -> r(),
            $fillColor -> g(),
            $fillColor -> b(),
            $fillColor -> a()
        );
        imagefilledellipse($this -> image_, $x, $y, $w, $h, $fc);
        
        if ($borderColor !== null) {
            $bc = imagecolorallocatealpha(
                $this -> image_,
                $borderColor -> r(),
                $borderColor -> g(),
                $borderColor -> b(),
                $borderColor -> a()
            );
            imageellipse($this -> image_, $x, $y, $w, $h, $bc);
        }
    }
    
    /**
     * @return Point2D|null
     */
    public function size(): ?Point2D
    {
        return $this -> size_;
    }
    
    /**
     * @return bool
     */
    public function gdOpen(): bool
    {
        if ($this -> image_ !== null) {
            return false;
        }
        
        $imgDat = getimagesize($this -> path());
        $w = $imgDat[0];
        $h = $imgDat[1];
        $mime = $imgDat['mime'];
        $bits = $imgDat['bits'];
        
        $this -> size_ = new Point2D($w, $h);
        $this -> bits_ = (int)$bits;
        $this -> mime_ = $mime;
        
        $src = $this -> imageCreate();
        if ($src === false) {
            return false;
        }
        $dest = imagecreatetruecolor($w, $h);
        imagecopy($dest, $src, 0, 0, 0, 0, $w, $h);
        
        $this -> image_ = $dest;
        return true;
    }
    
    /**
     * @param  \UT_Php\IO\File $file
     * @return bool
     */
    public function gdSaveAs(\UT_Php\IO\File $file): bool
    {
        if ($this -> image_ === null) {
            return false;
        }
        $newImg = Image::getImage($file);
        return $newImg -> imageSave($this -> image_);
    }
}
