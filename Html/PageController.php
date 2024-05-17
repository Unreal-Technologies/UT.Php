<?php
namespace UT_Php\Html;

abstract class PageController
{
    /**
     * @var string;
     */
    private $title = 'nyi';
    
    /**
     * @var UT_Php\Interfaces\IDirectory
     */
    private $root;
    
    /**
     * @var UT_Php\Interfaces\IFile[]
     */
    private $css = [];


    public abstract function initialize(): void;
    public abstract function setup(string &$title, array &$css): void;
    public abstract function render(): string;
    
    /**
     * @param \UT_Php\Interfaces\IDirectory $root
     */
    public final function __construct(\UT_Php\Interfaces\IDirectory $root) 
    {
        $this -> root = $root;
        $this -> css[] = \UT_Php\IO\File::fromString('default.css');
        
        $this -> initialize();
        $this -> setup($this -> title, $this -> css);
    }
    
    /**
     * @return string
     */
    public final function __toString(): string 
    {
        $html = '<!DOCTYPE>';
        $html .= '<html>';
        $html .= '<head>';
        foreach($this -> css as $file)
        {
            if($file -> exists())
            {
                $html .= '<link rel="stylesheet" type="text/css" href="'.$file -> relativeTo($this -> root).'"/>';
            }
        }
        $html .= '<title>'.$this -> title.'</title>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= $this -> render();
        $html .= '</body>';
        $html .= '</html>';
        
        return $html;
    }
}