<?php

namespace UT_Php_Core\IO\Common;

interface IXmlFile extends \UT_Php_Core\IO\IFile
{
    public function document(): ?\UT_Php_Core\IO\Xml\IXmlDocument;
}
