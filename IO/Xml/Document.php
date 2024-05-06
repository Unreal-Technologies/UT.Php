<?php
namespace UT_Php\IO\Xml;

require_once 'Element.php';

final class Document extends Element
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
     * @param string  $name
     * @param Doctype $doctype
     */
    function __construct(string $name, Doctype $doctype=null)
    {
        $this -> closed = false;
        parent::__construct($name);
        if($doctype === null) {
            $doctype = Doctype::Xml();
        }
        $this -> doctype = $doctype;
    }
    
    /**
     * @return string
     */
    function __toString(): string
    {
        $xml = $this -> doctype."\r\n";
        $xml .= parent::__toString();
        return $xml;
    }
    
    /**
     * @return \Element
     */
    final public function AsElement(): Element
    {
        $element = new Element($this -> Name());
        $children = $this -> Search('/^'.$this -> Id().'$/', null, self::Search_Parent, false);
        foreach($children as $child)
        {
            $element -> AddChild(clone $child);
        }
        
        return $element;
    }
    
    /**
     * @return Doctype
     */
    final public function Doctype(): Doctype
    {
        return $this -> doctype;
    }
    
    /**
     * @param  boolean $value default null
     * @return null|boolean
     */
    final public function Closed(bool $value=null): ?bool
    {
        if($value === null) {
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
    final public function ValidateDTDStream(string $stream, string $root, bool $output=true, string $encoding='utf-8'): bool
    {
        $file = abs(crc32(date('U').rand(0, 0xfff))).'.b';
        file_put_contents($file, $stream);
        
        $res = $this -> ValidateDTD($file, $root, $output, $encoding);
        unlink($file);
        return $res;
    }
    
    /**
     * @param  string  $stream
     * @param  boolean $output default true
     * @return boolean
     */
    final public function ValidateXSDStream(string $stream, bool $output=true): bool
    {
        $file = abs(crc32(date('U').rand(0, 0xfff))).'.b';
        file_put_contents($file, $stream);
        
        $res = $this -> ValidateXSD($file, $output);
        unlink($file);
        return $res;
    }
    
    /**
     * @param  \Data\IO\File $xsdSchemaFile
     * @param  boolean       $output
     * @return boolean
     */
    final public function ValidateXSD(\Data\IO\File $xsdSchemaFile, bool $output=true): bool
    {
        $xml = (string)$this;
        
        $dom = new DOMDocument();
        $dom -> loadXML($xml);
        
        $result = $output ? $dom -> schemaValidate($xsdSchemaFile) : @$dom -> schemaValidate($xsdSchemaFile);
        if(!$result) {
            echo $xml;
        }
        
        return $result;
    }
    
    /**
     * @param  string  $dtdSchemaFile
     * @param  string  $root
     * @param  boolean $output        default true
     * @param  string  $encoding      default utf-8
     * @return boolean
     */
    final public function ValidateDTD(\Data\IO\File $dtdSchemaFile, string $root, bool $output=true, string $encoding='utf-8'): bool
    {
        $xml = (string)$this;
        if(!file_exists($dtdSchemaFile)) {
            $dtdSchemaFile = dirname(__FILE__).'/'.$dtdSchemaFile;
        }
        
        $dtd = file_get_contents($dtdSchemaFile);
        
        $systemId = 'data://text/plain;base64,'.base64_encode($dtd);
        
        $old = new DOMDocument();
        $old -> loadXML($xml);
        
        $creator = new DOMImplementation();
        $docType = $creator -> createDocumentType($root, null, $systemId);
        $new = $creator -> createDocument(null, null, $docType);
        $new -> encoding = $encoding;
        
        $oldNode = $old -> getElementsByTagName($root) -> item(0);
        $newNode = $new -> importNode($oldNode, true);
        $new -> appendChild($newNode);
        
        return $output ? $new -> validate() : @$new -> validate();
    }
}