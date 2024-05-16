<?php

namespace UT_Php\Interfaces;

interface IFile extends IDiskManager
{
    public function relativeTo(IDirectory $dir): ?string;
    public function copyTo(IDirectory $dir, string $name = null): bool;
    public function parent(): ?IDirectory;
    public function extension(): string;
    public function basename(): string;
    public function contains(string $regex): bool;
}
