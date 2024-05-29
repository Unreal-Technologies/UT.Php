<?php

namespace UT_Php_Core\Routing;

interface IRouter
{
    public function add(RequestMethods $method, string $url, \Closure $target): void;
    public function match(): void;
    public function root(): \UT_Php_Core\IO\IDirectory;
}
