<?php
namespace UT_Php\IO\Xml;

final class Doctype
{
    /**
     * @var string
     */
    private $start;
    
    /**
     * @var string
     */
    private $end;
    
    /**
     * @var array
     */
    private $attributes;
    
    /**
     * @param string $start
     * @param string $end
     * @param array $attributes
     */
    function __construct(string $start, string $end, array $attributes)
    {
        $this -> start = $start;
        $this -> end = $end;
        $this -> attributes = $attributes;
    }
    
    /**
     * @return string
     */
    function __toString(): string
    {
        $key = key($this -> attributes);
        if(!is_numeric($key))
        {
            $list = array();
            foreach($this -> attributes as $key => $value)
            {
                $list[] = $key.'="'.$value.'"';
            }
            return $this -> start.(isset($list[0]) ? ' ' : null).implode(' ',$list).' '.$this  -> end;
        }
        else
        {
            return $this -> start.(isset($this -> attributes[0]) ? ' ' : null).implode(' ',$this -> attributes).' '.$this  -> end;
        }
    }

    /**
     * @return array
     */
    public function Attributes(): array
    {
        return $this -> attributes;
    }
    
    /**
     * @param string $version
     * @param string $encoding
     * @param boolean $standalone
     * @return Doctype
     */
    public static function Xml(string $version='1.0', string $encoding='utf-8', bool $standalone=true) : Doctype
    {
        $attr = array();
        $attr['version'] = $version;
        $attr['encoding'] = $encoding;
        $attr['standalone'] = $standalone ? 'yes' : 'no';
        
        return new Doctype('<?xml', '?>', $attr);
    }
    
    /**
     * @return Doctype
     */
    public static function Html5(): Doctype
    {
        return new Doctype('<!DOCTYPE html', '>', array());
    }
    
    /**
     * @return Doctype
     */
    public static function Html4_01Strict(): Doctype
    {
        $attr = array();
        $attr[] = '-//W3C//DTD HTML 4.01//EN';
        $attr[] = 'http://www.w3.org/TR/html4/strict.dtd';
        
        return new Doctype('<!DOCTYPE HTML PUBLIC', '>', $attr);
    }
    
    /**
     * @return Doctype
     */
    public static function Html4_01Transitional(): Doctype
    {
        $attr = array();
        $attr[] = '-//W3C//DTD HTML 4.01 Transitional//EN';
        $attr[] = 'http://www.w3.org/TR/html4/loose.dtd';
        
        return new Doctype('<!DOCTYPE HTML PUBLIC', '>', $attr);
    }
    
    /**
     * @return Doctype
     */
    public static function Html4_01Frameset(): Doctype
    {
        $attr = array();
        $attr[] = '-//W3C//DTD HTML 4.01 Frameset//EN';
        $attr[] = 'http://www.w3.org/TR/html4/frameset.dtd';
        
        return new Doctype('<!DOCTYPE HTML PUBLIC', '>', $attr);
    }
    
    /**
     * @return Doctype
     */
    public static function XHtml1_0Strict(): Doctype
    {
        $attr = array();
        $attr[] = '-//W3C//DTD XHTML 1.0 Strict//EN';
        $attr[] = 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd';
        
        return new Doctype('<!DOCTYPE html PUBLIC', '>', $attr);
    }
    
    /**
     * @return Doctype
     */
    public static function XHtml1_0Transitional(): Doctype
    {
        $attr = array();
        $attr[] = '-//W3C//DTD XHTML 1.0 Transitional//EN';
        $attr[] = 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd';
        
        return new Doctype('<!DOCTYPE html PUBLIC', '>', $attr);
    }
    
    /**
     * @return Doctype
     */
    public static function XHtml1_0Frameset(): Doctype
    {
        $attr = array();
        $attr[] = '-//W3C//DTD XHTML 1.0 Frameset//EN';
        $attr[] = 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd';
        
        return new Doctype('<!DOCTYPE html PUBLIC', '>', $attr);
    }
    
    /**
     * @return Doctype
     */
    public static function XHtml1_1(): Doctype
    {
        $attr = array();
        $attr[] = '-//W3C//DTD XHTML 1.1//EN';
        $attr[] = 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd';
        
        return new Doctype('<!DOCTYPE html PUBLIC', '>', $attr);
    }
}
