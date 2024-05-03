<?php
namespace UT_Php\IO\Xml;

class Element
{
    const Search_Name = 'name';
    const Search_Id = 'id';
    const Search_Text = 'text';
    const Search_Position = 'position';
    const Search_Parent = 'parent';
    const Search_Attributes = 'attributes';
    
    /**
     * @var array
     */
    private $attributes = null;
    
    /**
     * @var array
     */
    private $children = null;
    
    /**
     * @var string
     */
    private $text = null;
    
    /**
     * @var string
     */
    private $name = null;
    
    /**
     * @var string
     */
    private $id = null;
    
    /**
     * @var string
     */
    private $parent = null;
    
    /**
     * @var int
     */
    private $position = null;
    
    /**
     * @param string $name
     */
    function __construct(string $name)
    {
        $this -> attributes = array();
        $this -> name = $name;
        $this -> children = array();
        $this -> id = get_class().$name.rand(0,0xffff);
        $this -> position = 0;
    }
    
    /**
     * @return void
     */
    function __clone(): void
    {
        foreach($this -> children as $index => $child)
        {
            $this -> children[$index] = clone $child;
        }
    }
    
    /**
     * @param Element $element
     * @return boolean
     */
    function Remove(Element $element): bool
    {
        if($element -> Parent() !== $this -> id)
        {
            return false;
        }
        
        $pos = -1;
        foreach($this -> children as $index => $child)
        {
            if($child -> Id() === $element -> Id())
            {
                $pos = $index;
                break;
            }
        }
        if($pos !== -1)
        {
            unset($this -> children[$pos]);
            return true;
        }
        return false;
    }
    
    /**
     * @return string
     */
    function __toString(): string
    {
        $xml = '';
        $tab = str_repeat("\t", $this -> position);
        
        $list = array();
        foreach($this -> attributes as $key => $value)
        {
            $list[] = $key.'="'.$value.'"';
        }
        $attributeString = (isset($list[0]) ? ' ' : null).implode(' ', $list);
        
        if($this -> text === null && !isset($this -> children[0]))
        {
            $xml .= $tab.'<'.$this -> name.''.$attributeString.'/>'."\r\n";
        }
        elseif($this -> text !== null)
        {
            $xml .= $tab.'<'.$this -> name.''.$attributeString.'>'.$this -> text.'</'.$this -> name.'>'."\r\n";
        }
        else
        {
            $xml .= $tab.'<'.$this -> name.''.$attributeString.'>'."\r\n";
            foreach($this -> children as $child)
            {
                $xml .= $child;
            }
            $xml .= $tab.'</'.$this -> name.'>'."\r\n";
        }
        return $xml;
    }
    
    /**
     * @param string $xmlstring
     * @return Element
     */
    final public static function CreateFromXml(string $xmlstring, Doctype $doctype=null): Element
    {
        $xml = simplexml_load_string($xmlstring);
        return Element::CreateFromSimpleXml($xml, $doctype);
    }
    
    /**
     * @param SimpleXMLElement $element
     * @return Element
     */
    final public static function CreateFromSimpleXml(\SimpleXMLElement $element, Doctype $doctype=null): Element
    {
        $node = new Element($element -> getName());
        foreach($element -> attributes() as $key => $value)
        {
            $node -> attributes[$key] = (string)$value;
        }
        foreach($element -> children() as $child)
        {
            $node -> AddChild(Element::CreateFromSimpleXml($child));
        }
        $string = (string)$element;
        if($string !== null && $string !== '')
        {
            $node -> Text($string);
        }
        
        return $doctype == null ? $node : $node -> AsDocument($doctype);
    }

    /**
     * @return array
     */
    final public function Attributes(array $list=null): array
    {
        if($list !== null)
        {
            $this -> attributes = array_merge($this -> attributes, $list);
        }
        
        return $this -> attributes;
    }
    
    /**
     * @return string
     */
    final public function Parent(): string
    {
        return $this -> parent;
    }
    
    /**
     * @return string
     */
    final public function Id(): string
    {
        return $this -> id;
    }
    
    /** 
     * @param string $text
     * @return string
     */
    private function AmpParser(string $text): string
    {
        $apos = strpos($text, '&');
        while($apos !== false)
        {
            $qpos = strpos($text, ';', $apos);
            if($qpos === false)
            {
                $left = substr($text, 0, $apos);
                $right = substr($text, $apos + 1);
                $text = $left.'&amp;'.$right;
            }
            else
            {
                $spos = strpos($text, ' ', $apos);
                if($spos < $qpos && !$qpos)
                {
                    var_dump($text);
                    var_dump($apos);
                    var_dump($qpos);
                    var_dump($spos);
                    echo '-----------'."\n";
                }
            }
            $apos = strpos($text, '&', $apos + 1);
        }

        return $text;
    }
    
    /**
     * @param string $text
     * @return null|string
     */
    private function TextParser(string $text): ?string
    {
        if($text === null || trim($text) === '')
        {
            return null;
        }
        return $this -> AmpParser(str_replace('<br />', "\n", $text));
    }
    
    /**
     * @param string $text
     * @return string
     */
    final public function Text(string $text=null): ?string
    {
        if($text === null)
        {
            return $this -> text;
        }
        if(count($this -> children) === 0)
        {
            $this -> text = $this -> TextParser($text);
        }
        return null;
    }
    
    /**
     * @return string
     */
    final public function Name(): string
    {
        return $this -> name;
    }
    
    /**
     * @param string $name
     * @return Element|null
     */
    final public function CreateChild(string $name): ?Element
    {
        if($this -> text === null)
        {
            $element = new Element($name);
            $this -> AddChild($element);
            return $element;
        }
        return null;
    }
    
    /**
     * @param Element $element
     * @return boolean
     */
    final public function AddChild(Element $element): bool
    {
        if($this -> text === null)
        {
            $element -> parent = $this -> id;
            $element -> UpdatePosition($this -> position + 1);
            $this -> children[] = $element;
            return true;
        }
        return false;
    }
    
    /**
     * @param Doctype $doctype default null
     * @return \Document
     */
    final public function AsDocument(Doctype $doctype=null): Document
    {
        if($doctype === null)
        {
            $doctype = Doctype::Xml();
        }
        
        $children = $this -> Search('/^'.$this -> id.'$/', null, self::Search_Parent, false);
        $doc = new Document($this -> name, $doctype);
        foreach($children as $child)
        {
            $doc -> AddChild(clone $child);
        }

        return $doc;
    }
    
    /**
     * @param string $element
     * @param int $returnIndex default null
     * @param string $type default self::Search_Name
     * @return array|Element|null
     */
    final public function Search(string $regex, int $returnIndex=null, string $type=self::Search_Name, $recursive=true, $recursivePos=0): ?array
    {
        $list = array();
        if($type == self::Search_Attributes)
        {
            $keys = array_keys($this -> attributes);
            foreach($keys as $key)
            {
                if(preg_match($regex, $key))
                {
                    $list[] = $this;
                }
            }
        }
        elseif(preg_match($regex, $this -> $type))
        {
            $list[] = $this;
        }
        if($recursive || (!$recursive && $recursivePos === 0))
        {
            foreach($this -> children as $child)
            {
                $result = $child -> Search($regex, null, $type, $recursive, $recursivePos + 1);
                if($result !== null)
                {
                    if(is_array($result))
                    {
                        $list = array_merge($list, $result);
                    }
                    else
                    {
                        $list[] = $result;
                    }
                }
            }
        }
        return !isset($list[0]) ? null : ($returnIndex === null ? $list : [$list[$returnIndex]]);
    }
    
    /**
     * @param int $pos
     */
    final public function UpdatePosition(int $pos): void
    {
        $this -> position = $pos;
        foreach($this -> children as $child)
        {
            $child -> UpdatePosition($pos + 1);
        }
    }
}
