<?php

namespace UT_Php\Interfaces;

interface IXmlFile extends IFile
{
    public function document(): ?IXmlDocument;
}
