<?php

namespace UT_Php_Core\IO\Common\Php;

interface IMethod extends IPhpParser
{
    public function __construct(array $head, ?array $body = null);
    public function body(): ?string;
    public function isPublic(): bool;
    public function isPrivate(): bool;
    public function isProtected(): bool;
    public function replace(string $old, string $new, ReplaceTypes $type): void;
    public function variables(bool $includeParameters = true): array;
    public function strip(): void;
}
