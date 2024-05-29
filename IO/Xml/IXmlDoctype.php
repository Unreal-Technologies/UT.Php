<?php

namespace UT_Php_Core\IO\Xml;

interface IXmlDoctype
{
    public function __toString(): string;
    public function attributes(): array;
}
