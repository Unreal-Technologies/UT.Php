<?php

namespace UT_Php\Interfaces;

interface IRouter
{
    public function add(\UT_Php\Enums\RequestMethods $method, string $url, \Closure $target): void;
    public function match(): void;
    public function root(): \UT_Php\Interfaces\IDirectory;
}
