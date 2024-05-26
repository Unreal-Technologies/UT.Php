<?php

namespace UT_Php_Core\IO\Xml;

interface IXmlDocument extends IXmlElement
{
    public function __toString(): string;
    public function asElement(): IXmlElement;
    public function doctype(): IXmlDoctype;
    public function closed(bool $value = null): ?bool;
    public function validateDtdStream(
        string $stream,
        string $root,
        bool $output = true,
        string $encoding = 'utf-8'
    ): bool;
    public function validateXsdStream(string $stream, bool $output = true): bool;
    public function validateXsd(\UT_Php_Core\IO\IFile $xsdSchemaFile, bool $output = true): bool;
    public function validateDtd(
            \UT_Php_Core\IO\Common\IDtdFile $dtdSchemaFile,
        string $root,
        bool $output = true,
        string $encoding = 'utf-8'
    ): bool;
}
