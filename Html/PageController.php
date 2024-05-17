<?php
namespace UT_Php\Html;

abstract class PageController
{
    /**
     * @var string;
     */
    private $title = 'nyi';
    
    /**
     * @var UT_Php\Interfaces\IFile[]
     */
    private $css = [];
    
    /**
     * @var \UT_Php\Interfaces\IRouter
     */
    private $router;


    public abstract function initialize(): void;
    public abstract function setup(string &$title, array &$css): void;
    public abstract function render(): string;
    
    /**
     * @return \UT_Php\Interfaces\IDirectory
     */
    protected final function root(): \UT_Php\Interfaces\IDirectory
    {
        return $this -> router -> root();
    }
    
    /**
     * @param \UT_Php\Interfaces\IRouter
     */
    public final function __construct(\UT_Php\Interfaces\IRouter $router) 
    {
        $this -> router = $router;
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
                $html .= '<link rel="stylesheet" type="text/css" href="'.$file -> relativeTo($this -> root()).'"/>';
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