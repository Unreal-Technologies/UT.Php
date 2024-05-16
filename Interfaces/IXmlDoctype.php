<?php

namespace UT_Php\Interfaces;

interface IXmlDoctype
{
    public function __toString(): string;
    public function attributes(): array;
}
