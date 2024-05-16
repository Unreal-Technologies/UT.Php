<?php

namespace UT_Php\IO\Xml;

final class Document extends Element implements \UT_Php\Interfaces\IXmlDocument
{
    /**
     * @var Doctype
     */
    private $doctype;

    /**
     * @var boolean
     */
    private $closed;

    /**
     * @param string $name
     * @param \UT_Php\Interfaces\IXmlDoctype $doctype
     */
    public function __construct(string $name, \UT_Php\Interfaces\IXmlDoctype $doctype = null)
    {
        $this -> closed = false;
        parent::__construct($name);
        if ($doctype === null) {
            $doctype = Doctype::xml();
        }
        $this -> doctype = $doctype;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $xml = $this -> doctype . "\r\n";
        $xml .= parent::__toString();
        return $xml;
    }

    /**
     * @return \UT_Php\Interfaces\IXmlElement
     */
    final public function asElement(): \UT_Php\Interfaces\IXmlElement
    {
        $element = new Element($this -> name());
        $children = $this -> search('/^' . $this -> id() . '$/', null, self::SEARCH_PARENT, false);
        foreach ($children as $child) {
            $element -> addChild(clone $child);
        }

        return $element;
    }

    /**
     * @return \UT_Php\Interfaces\IXmlDoctype
     */
    final public function doctype(): \UT_Php\Interfaces\IXmlDoctype
    {
        return $this -> doctype;
    }

    /**
     * @param  boolean $value default null
     * @return null|boolean
     */
    final public function closed(bool $value = null): ?bool
    {
        if ($value === null) {
            return $this -> closed;
        }
        $this -> closed = $value;
        return null;
    }

    /**
     * @param  string  $stream
     * @param  string  $root
     * @param  boolean $output
     * @param  string  $encoding
     * @return boolean
     */
    final public function validateDtdStream(
        string $stream,
        string $root,
        bool $output = true,
        string $encoding = 'utf-8'
    ): bool {
        $file = abs(crc32(date('U') . rand(0, 0xfff))) . '.b';
        file_put_contents($file, $stream);

        $res = $this -> validateDtd($file, $root, $output, $encoding);
        unlink($file);
        return $res;
    }

    /**
     * @param  string  $stream
     * @param  boolean $output default true
     * @return boolean
     */
    final public function validateXsdStream(string $stream, bool $output = true): bool
    {
        $file = abs(crc32(date('U') . rand(0, 0xfff))) . '.b';
        file_put_contents($file, $stream);

        $res = $this -> validateXsd($file, $output);
        unlink($file);
        return $res;
    }

    /**
     * @param  \Data\IO\File $xsdSchemaFile
     * @param  boolean       $output
     * @return boolean
     */
    final public function validateXsd(\UT_Php\Interfaces\IFile $xsdSchemaFile, bool $output = true): bool
    {
        $xml = (string)$this;

        $dom = new DOMDocument();
        $dom -> loadXML($xml);

        $result = $output ?
            $dom -> schemaValidate($xsdSchemaFile -> path()) :
            @$dom -> schemaValidate($xsdSchemaFile -> path());
        if (!$result) {
            echo $xml;
        }

        return $result;
    }

    /**
     * @param \UT_Php\Interfaces\IDtdFile $dtdSchemaFile
     * @param string $root
     * @param bool $output
     * @param string $encoding
     * @return bool
     */
    final public function validateDtd(
        \UT_Php\Interfaces\IDtdFile $dtdSchemaFile,
        string $root,
        bool $output = true,
        string $encoding = 'utf-8'
    ): bool {
        $xml = (string)$this;
        if (!$dtdSchemaFile -> exists()) {
            return false;
        }

        $systemId = $dtdSchemaFile -> systemId();

        $old = new \DOMDocument();
        $old -> loadXML($xml);

        $creator = new \DOMImplementation();
        $docType = $creator -> createDocumentType($root, null, $systemId);
        $new = $creator -> createDocument(null, null, $docType);
        $new -> encoding = $encoding;

        $oldNode = $old -> getElementsByTagName($root) -> item(0);
        $newNode = $new -> importNode($oldNode, true);
        $new -> appendChild($newNode);

        return $output ? $new -> validate() : @$new -> validate();
    }
}
