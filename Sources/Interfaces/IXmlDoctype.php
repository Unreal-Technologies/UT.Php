<?php

namespace UT_Php_Core\Interfaces;

interface IXmlDoctype
{
    public function __toString(): string;
    public function attributes(): array;
}
