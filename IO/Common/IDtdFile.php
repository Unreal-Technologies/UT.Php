<?php

namespace UT_Php_Core\IO\Common;

interface IDtdFile extends \UT_Php_Core\Interfaces\IFile
{
    public function systemId(): ?string;
}
