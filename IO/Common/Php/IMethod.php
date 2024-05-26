<?php

namespace UT_Php_Core\IO\Common\Php;

interface IMethod extends IPhpParser
{
    public function __construct(array $head, ?array $body = null);
    public function body(): string;
}
