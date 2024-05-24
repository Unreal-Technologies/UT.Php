<?php

namespace UT_Php_Core\Drawing;

abstract class Image extends \UT_Php_Core\IO\File implements \UT_Php_Core\Interfaces\IImage
{
    /**
     * @var \GdImage|null
     */
    private $image = null;

    /**
     * @var \UT_Php_Core\Interfaces\ISize2D|null
     */
    private $size = null;

    /**
     * @var int|null
     */
    private $bits = null;

    /**
     * @var string|null
     */
    private $mime = null;

    /**
     * @param \UT_Php_Core\Interfaces\IFile $file
     * @return Image|null
     * @throws \Exception
     */
    public static function getImage(\UT_Php_Core\Interfaces\IFile $file): ?\UT_Php_Core\Interfaces\IImage
    {
        $ext = strtolower($file -> extension());
        switch ($ext) {
            case 'bmp':
                return new Image\Bmp($file -> path());
            case 'png':
                return new Image\Png($file -> path());
            default:
                throw new \Exception('Extension "' . $ext . '" is not implemented');
        }
        return null;
    }

    abstract public function imageCreate(): mixed;
    abstract public function imageSave(\GdImage $image): bool;

    /**
     * @param \UT_Php_Core\Interfaces\IRectangle $rectangle
     * @param \UT_Php_Core\Interfaces\IColor $fillColor
     * @param \UT_Php_Core\Interfaces\IColor $borderColor
     * @return void
     */
    public function gdDrawRectangle(
        \UT_Php_Core\Interfaces\IRectangle $rectangle,
        \UT_Php_Core\Interfaces\IColor $fillColor,
        \UT_Php_Core\Interfaces\IColor $borderColor = null
    ): void {
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
            $this -> image,
            $fillColor -> r(),
            $fillColor -> g(),
            $fillColor -> b(),
            $fillColor -> a()
        );
        imagefilledrectangle($this -> image, $x1, $y1, $x2, $y2, $fc);

        if ($borderColor !== null) {
            $bc = imagecolorallocatealpha(
                $this -> image,
                $borderColor -> r(),
                $borderColor -> g(),
                $borderColor -> b(),
                $borderColor -> a()
            );
            imagerectangle($this -> image, $x1, $y1, $x2, $y2, $bc);
        }
    }

    /**
     * @param \UT_Php_Core\Interfaces\IRectangle $rectangle
     * @param \UT_Php_Core\Interfaces\IColor $fillColor
     * @param \UT_Php_Core\Interfaces\IColor $borderColor
     * @return void
     */
    public function gdDrawEllipse(
        \UT_Php_Core\Interfaces\IRectangle $rectangle,
        \UT_Php_Core\Interfaces\IColor $fillColor,
        \UT_Php_Core\Interfaces\IColor $borderColor = null
    ): void {
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
            $this -> image,
            $fillColor -> r(),
            $fillColor -> g(),
            $fillColor -> b(),
            $fillColor -> a()
        );
        imagefilledellipse($this -> image, $x, $y, $w, $h, $fc);

        if ($borderColor !== null) {
            $bc = imagecolorallocatealpha(
                $this -> image,
                $borderColor -> r(),
                $borderColor -> g(),
                $borderColor -> b(),
                $borderColor -> a()
            );
            imageellipse($this -> image, $x, $y, $w, $h, $bc);
        }
    }

    /**
     * @return \UT_Php_Core\Interfaces\ISize2D|null
     */
    public function size(): ?\UT_Php_Core\Interfaces\ISize2D
    {
        return $this -> size;
    }

    /**
     * @return bool
     */
    public function gdOpen(): bool
    {
        if ($this -> image !== null) {
            return false;
        }

        $imgDat = getimagesize($this -> path());
        $w = $imgDat[0];
        $h = $imgDat[1];
        $mime = $imgDat['mime'];
        $bits = $imgDat['bits'];

        $this -> size = new Point2D($w, $h);
        $this -> bits = (int)$bits;
        $this -> mime = $mime;

        $src = $this -> imageCreate();
        if ($src === false) {
            return false;
        }
        $dest = imagecreatetruecolor($w, $h);
        imagecopy($dest, $src, 0, 0, 0, 0, $w, $h);

        $this -> image = $dest;
        return true;
    }

    /**
     * @param  \UT_Php_Core\IO\File $file
     * @return bool
     */
    public function gdSaveAs(\UT_Php_Core\IO\File $file): bool
    {
        if ($this -> image === null) {
            return false;
        }
        $newImg = Image::getImage($file);
        return $newImg -> imageSave($this -> image);
    }
}
