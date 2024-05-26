<?php

namespace UT_Php_Core\IO;

interface IFile extends IDiskManager
{
    public function relativeTo(IDirectory $dir): ?string;
    public function copyTo(IDirectory $dir, string $name = null): bool;
    public function parent(): ?IDirectory;
    public function extension(): string;
    public function basename(): string;
    public function read(): string;
    public function write(string $stream): void;
    public static function fromString(string $path): \UT_Php_Core\Interfaces\IFile;
    public static function fromDirectory(\UT_Php_Core\Interfaces\IDirectory $dir, string $name): ?\UT_Php_Core\Interfaces\IFile;
    public static function fromFile(\UT_Php_Core\Interfaces\IFile $file): \UT_Php_Core\Interfaces\IFile;
    public function asBmp(): ?\UT_Php_Core\Interfaces\IBmpFile;
    public function asDtd(): ?\UT_Php_Core\Interfaces\IDtdFile;
    public function asPhp(): ?\UT_Php_Core\Interfaces\IPhpFile;
    public function asPng(): ?\UT_Php_Core\Interfaces\IPngFile;
    public function asXml(): ?\UT_Php_Core\Interfaces\IXmlFile;
}
