<?php

namespace UT_Php\Interfaces;

interface IDirectory extends IDiskManager
{
    public function create(): bool;
    public function list(string $regex = null, bool $refresh = false): array;
    public function read(?string &$out): bool;
    public function open(): bool;
    public function close(): void;
}
