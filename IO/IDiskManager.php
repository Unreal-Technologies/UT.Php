<?php
namespace UT_Php\IO;

interface IDiskManager
{
    public function Path(): string;
    public function Exists(): bool;
    public function Contains(string $data): bool;
    public function Name(): string;
}