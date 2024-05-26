<?php

namespace UT_Php_Core\IO\Common\Php;

interface IMember extends IPhpParser
{
    public function isPublic(): bool;
    public function isPrivate(): bool;
    public function isProtected(): bool;
}
