<?php

namespace UT_Php_Core\IO\Common\Php;

interface IPhpParser
{
    public function __construct(array $tokens, ?array $args = null);
    public function name(): string;
    public function declaration(): string;
}
