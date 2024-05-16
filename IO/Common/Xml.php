<?php

namespace UT_Php\IO\Common;

class Xml extends \UT_Php\IO\File implements \UT_Php\Interfaces\IXmlFile
{
    /**
     * @param string $path
     * @param bool $requiresExtension
     * @throws \Exception
     */
    public function __construct(string $path, bool $requiresExtension = true)
    {
        parent::__construct($path);

        if ($requiresExtension && strtolower($this -> extension()) != 'xml') {
            throw new \Exception('"' . $path . '" does not have the .xml extension');
        }
    }

    /**
     * @return \UT_Php\Interfaces\IXmlDocument|null
     */
    public function document(): ?\UT_Php\Interfaces\IXmlDocument
    {
        if (!$this -> exists()) {
            return null;
        }
        return \UT_Php\IO\Xml\Document::createFromFile($this) -> asDocument();
    }
}
