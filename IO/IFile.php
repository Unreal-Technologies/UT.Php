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
    public static function fromString(string $path): IFile;
    public static function fromDirectory(IDirectory $dir, string $name): ?IFile;
    public static function fromFile(IFile $file): IFile;
    public function asBmp(): ?Common\IBmpFile;
    public function asDtd(): ?Common\IDtdFile;
    public function asPhp(): ?Common\IPhpFile;
    public function asPng(): ?Common\IPngFile;
    public function asXml(): ?Common\IXmlFile;
}
