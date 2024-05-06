<?php
namespace UT_Php\IO;

interface IDiskManager
{
    public function path(): string;
    public function exists(): bool;
    public function contains(string $data): bool;
    public function name(): string;
}
