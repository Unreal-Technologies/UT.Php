<?php

namespace UT_Php_Core\Html;

abstract class PageController
{
    /**
     * @var string;
     */
    private $title = 'nyi';

    /**
     * @var UT_Php_Core\Interfaces\IFile[]
     */
    private $css = [];

    /**
     * @var \UT_Php_Core\Interfaces\IRouter
     */
    private $router;


    abstract public function initialize(): void;
    abstract public function setup(string &$title, array &$css): void;
    abstract public function render(): string;

    /**
     * @return \UT_Php_Core\Interfaces\IDirectory
     */
    final protected function root(): \UT_Php_Core\Interfaces\IDirectory
    {
        return $this -> router -> root();
    }

    /**
     * @param \UT_Php_Core\Interfaces\IRouter
     */
    final public function __construct(\UT_Php_Core\Interfaces\IRouter $router)
    {
        $this -> router = $router;
        $this -> css[] = \UT_Php_Core\IO\File::fromString('default.css');

        $this -> initialize();
        $this -> setup($this -> title, $this -> css);
    }

    /**
     * @return string
     */
    final public function __toString(): string
    {
        $html = '<!DOCTYPE>';
        $html .= '<html>';
        $html .= '<head>';
        foreach ($this -> css as $file) {
            if ($file -> exists()) {
                $html .= '<link rel="stylesheet" type="text/css" href="' . $file -> relativeTo($this -> root()) . '"/>';
            }
        }
        $html .= '<title>' . $this -> title . '</title>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= $this -> render();
        $html .= '</body>';
        $html .= '</html>';

        return $html;
    }
}
