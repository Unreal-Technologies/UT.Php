<?php

namespace UT_Php_Core\Interfaces;

interface IDirectory extends IDiskManager
{
    public function create(): bool;
    public function list(string $regex = null, bool $refresh = false): array;
    public function read(?string &$out): bool;
    public function open(): bool;
    public function close(): void;
    public function parent(): \UT_Php_Core\IO\Directory;
    public function contains(string $regex): bool;
}
