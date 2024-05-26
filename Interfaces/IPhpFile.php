<?php
namespace UT_Php_Core\Interfaces;

interface IPhpFile extends IFile
{
    public function tokens(): array;
    public function namespace(): ?\UT_Php_Core\IO\Common\Php\TokenNamespace;
}