<?php

namespace UT_Php_Core\IO\Common\Php;

interface IPhpParser
{
    public function __construct(array $tokens);
    public function name(): string;
    public function declaration(): string;
}
