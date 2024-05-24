<?php

namespace UT_Php_Core\Interfaces;

interface IXmlFile extends IFile
{
    public function document(): ?IXmlDocument;
}
