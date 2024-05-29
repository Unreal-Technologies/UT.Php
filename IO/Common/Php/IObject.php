<?php

namespace UT_Php_Core\IO\Common\Php;

interface IObject extends IPhpParser
{
    public function isClass(): bool;
    public function isInterface(): bool;
    public function isEnum(): bool;
    public function isTrait(): bool;
    public function isAbstract(): bool;
    public function isFinal(): bool;
    public function extends(): ?string;
    public function implements(): array;
}
