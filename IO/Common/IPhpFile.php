<?php

namespace UT_Php_Core\IO\Common;

interface IPhpFile extends \UT_Php_Core\IO\IFile
{
    public function namespace(): ?\UT_Php_Core\IO\Common\Php\TokenNamespace;
    public function object(): ?\UT_Php_Core\IO\Common\Php\TokenObject;
    public function traits(): array;
    public function cases(): array;
    public function members(): array;
    public function methods(): array;
}
