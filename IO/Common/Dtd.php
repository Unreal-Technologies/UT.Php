<?php

namespace UT_Php\IO\Common;

class Dtd extends \UT_Php\IO\File implements \UT_Php\Interfaces\IDtdFile
{
    /**
     * @param string $path
     * @param bool $requiresExtension
     * @throws \Exception
     */
    public function __construct(string $path, bool $requiresExtension = true)
    {
        parent::__construct($path);

        if ($requiresExtension && strtolower($this -> extension()) != 'dtd') {
            throw new \Exception('"' . $path . '" does not have the .dtd extension');
        }
    }

    /**
     * @return \UT_Php\IO\Xml\Document|null
     */
    public function systemId(): ?string
    {
        if (!$this -> exists()) {
            return null;
        }
        return 'data://text/plain;base64,' . base64_encode($this -> content());
    }
}
