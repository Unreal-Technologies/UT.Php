<?php

namespace UT_Php_Core\Drawing;

interface IImage extends IFile
{
    public function imageCreate(): mixed;
    public function imageSave(\GdImage $image): bool;
    public function gdDrawRectangle(IRectangle $rectangle, IColor $fillColor, IColor $borderColor = null): void;
    public function gdDrawEllipse(IRectangle $rectangle, IColor $fillColor, IColor $borderColor = null): void;
    public function size(): ?ISize2D;
    public function gdOpen(): bool;
    public function gdSaveAs(\UT_Php_Core\IO\File $file): bool;
}
