<?php

namespace UT_Php_Core\Interfaces;

interface ILinq
{
    public function where(\Closure $lambda): ILinq;
    public function select(\Closure $lambda): ILinq;
    public function groupBy(\Closure $lambda): ILinq;
    public function toArray(\Closure $lambda = null): array;
    public function firstOrDefault(\Closure $lambda = null): mixed;
    public function count(): int;
    public function sum(\Closure $lambda = null): ILinq;
    public function avg(\Closure $lambda = null): ILinq;
    public function skip(int $count): \UT_Php_Core\Interfaces\ILinq;
    public function orderBy(
        \Closure $lambda = null,
        \UT_Php_Core\Enums\SortDirections $direction = \UT_Php_Core\Enums\SortDirections::Asc
    ): ILinq;
}
