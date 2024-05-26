<?php

namespace UT_Php_Core\Drawing;

abstract class Image extends \UT_Php_Core\IO\File implements IImage
{
    /**
     * @var \GdImage|null
     */
    private mixed $image = null;

    /**
     * @var ISize2D|null
     */
    private ?ISize2D $size = null;

    /**
     * @var int|null
     */
    private ?int $bits = null;

    /**
     * @var string|null
     */
    private ?string $mime = null;

    /**
     * @param \UT_Php_Core\IO\IFile $file
     * @return Image|null
     * @throws \Exception
     */
    public static function getImage(\UT_Php_Core\IO\IFile $file): ?IImage
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
     * @param IRectangle $rectangle
     * @param IColor $fillColor
     * @param IColor $borderColor
     * @return void
     */
    public function gdDrawRectangle(
        IRectangle $rectangle,
        IColor $fillColor,
        IColor $borderColor = null
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
     * @param IRectangle $rectangle
     * @param IColor $fillColor
     * @param IColor $borderColor
     * @return void
     */
    public function gdDrawEllipse(
        IRectangle $rectangle,
        IColor $fillColor,
        IColor $borderColor = null
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
     * @return ISize2D|null
     */
    public function size(): ?ISize2D
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
     * @param  \UT_Php_Core\IO\IFile $file
     * @return bool
     */
    public function gdSaveAs(\UT_Php_Core\IO\IFile $file): bool
    {
        if ($this -> image === null) {
            return false;
        }
        $newImg = Image::getImage($file);
        return $newImg -> imageSave($this -> image);
    }
}
