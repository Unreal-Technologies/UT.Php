<?php

namespace UT_Php_Core\Interfaces;

interface IRouter
{
    public function add(\UT_Php_Core\Enums\RequestMethods $method, string $url, \Closure $target): void;
    public function match(): void;
    public function root(): \UT_Php_Core\Interfaces\IDirectory;
}
