<?php

namespace UT_Php_Core\IO\Common;

interface IXmlFile extends \UT_Php_Core\Interfaces\IFile
{
    public function document(): ?IXmlDocument;
}
