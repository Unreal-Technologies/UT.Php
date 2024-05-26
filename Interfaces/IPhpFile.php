<?php

namespace UT_Php_Core\Interfaces;

interface IPhpFile extends IFile
{
    public function namespace(): ?\UT_Php_Core\IO\Common\Php\TokenNamespace;
    public function object(): ?\UT_Php_Core\IO\Common\Php\TokenObject;
    public function traits(): array;
    public function cases(): array;
}
