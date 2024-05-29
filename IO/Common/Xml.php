<?php

namespace UT_Php_Core\IO\Common;

class Xml extends \UT_Php_Core\IO\File implements IXmlFile
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
     * @return \UT_Php_Core\IO\Xml\Document
     */
    public function document(): ?\UT_Php_Core\IO\Xml\Document
    {
        if (!$this -> exists()) {
            return null;
        }
        return \UT_Php_Core\IO\Xml\Document::createFromFile($this) -> asDocument();
    }
}
