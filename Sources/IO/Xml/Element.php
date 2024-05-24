<?php

namespace UT_Php_Core\IO\Xml;

class Element implements \UT_Php_Core\Interfaces\IXmlElement
{
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
    public function __construct(string $name)
    {
        $this -> attributes = array();
        $this -> name = $name;
        $this -> children = array();
        $this -> id = get_class() . $name . rand(0, 0xffff);
        $this -> position = 0;
    }

    /**
     * @return Element[]
     */
    public function children(): array
    {
        return $this -> children;
    }

    /**
     * @return void
     */
    public function __clone(): void
    {
        foreach ($this -> children as $index => $child) {
            $this -> children[$index] = clone $child;
        }
    }

    /**
     * @param \UT_Php_Core\Interfaces\IXmlElement $element
     * @return bool
     */
    public function remove(\UT_Php_Core\Interfaces\IXmlElement $element): bool
    {
        if ($element -> parent() !== $this -> id) {
            return false;
        }

        $pos = -1;
        foreach ($this -> children as $index => $child) {
            if ($child -> id() === $element -> id()) {
                $pos = $index;
                break;
            }
        }
        if ($pos !== -1) {
            unset($this -> children[$pos]);
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $xml = '';
        $tab = str_repeat("\t", $this -> position);

        $list = array();
        foreach ($this -> attributes as $key => $value) {
            $list[] = $key . '="' . $value . '"';
        }
        $attributeString = (isset($list[0]) ? ' ' : null) . implode(' ', $list);

        if ($this -> text === null && !isset($this -> children[0])) {
            $xml .= $tab . '<' . $this -> name . '' . $attributeString . '/>' . "\r\n";
        } elseif ($this -> text !== null) {
            $xml .= $tab .
                '<' . $this -> name . '' . $attributeString . '>' .
                $this -> text .
                '</' . $this -> name . '>' . "\r\n";
        } else {
            $xml .= $tab . '<' . $this -> name . '' . $attributeString . '>' . "\r\n";
            foreach ($this -> children as $child) {
                $xml .= $child;
            }
            $xml .= $tab . '</' . $this -> name . '>' . "\r\n";
        }
        return $xml;
    }

    /**
     * @param \UT_Php_Core\IO\File $file
     * @param Doctype $doctype
     * @return Element
     */
    final public static function createFromFile(\UT_Php_Core\IO\Common\Xml $file, Doctype $doctype = null): Element
    {
        return Element::createFromXml($file -> content(), $doctype);
    }

    /**
     * @param  string $xmlstring
     * @return Element
     */
    final public static function createFromXml(string $xmlstring, Doctype $doctype = null): Element
    {
        $xml = simplexml_load_string($xmlstring);
        return Element::createFromSimpleXml($xml, $doctype);
    }

    /**
     * @param  SimpleXMLElement $element
     * @return Element
     */
    final public static function createFromSimpleXml(\SimpleXMLElement $element, Doctype $doctype = null): Element
    {
        $node = new Element($element -> getName());
        foreach ($element -> attributes() as $key => $value) {
            $node -> attributes[$key] = (string)$value;
        }
        foreach ($element -> children() as $child) {
            $node -> addChild(Element::createFromSimpleXml($child));
        }
        $string = (string)$element;
        if ($string !== null && $string !== '') {
            $node -> text($string);
        }

        return $doctype == null ? $node : $node -> asDocument($doctype);
    }

    /**
     * @return array
     */
    final public function attributes(array $list = null): array
    {
        if ($list !== null) {
            $this -> attributes = array_merge($this -> attributes, $list);
        }

        return $this -> attributes;
    }

    /**
     * @return string
     */
    final public function parent(): string
    {
        return $this -> parent;
    }

    /**
     * @return string
     */
    final public function id(): string
    {
        return $this -> id;
    }

    /**
     * @param  string $text
     * @return string
     */
    private function ampParser(string $text): string
    {
        $apos = strpos($text, '&');
        while ($apos !== false) {
            $qpos = strpos($text, ';', $apos);
            if ($qpos === false) {
                $left = substr($text, 0, $apos);
                $right = substr($text, $apos + 1);
                $text = $left . '&amp;' . $right;
            } else {
                $spos = strpos($text, ' ', $apos);
                if ($spos < $qpos && !$qpos) {
                    var_dump($text);
                    var_dump($apos);
                    var_dump($qpos);
                    var_dump($spos);
                    echo '-----------' . "\n";
                }
            }
            $apos = strpos($text, '&', $apos + 1);
        }

        return $text;
    }

    /**
     * @param  string $text
     * @return null|string
     */
    private function textParser(string $text): ?string
    {
        if ($text === null || trim($text) === '') {
            return null;
        }
        return $this -> ampParser(str_replace('<br />', "\n", $text));
    }

    /**
     * @param  string $text
     * @return string
     */
    final public function text(string $text = null): ?string
    {
        if ($text === null) {
            return $this -> text;
        }
        if (count($this -> children) === 0) {
            $this -> text = $this -> textParser($text);
        }
        return null;
    }

    /**
     * @return string
     */
    final public function name(): string
    {
        return $this -> name;
    }

    /**
     * @param  string $name
     * @return Element|null
     */
    final public function createChild(string $name): ?\UT_Php_Core\Interfaces\IXmlElement
    {
        if ($this -> text === null) {
            $element = new Element($name);
            $this -> addChild($element);
            return $element;
        }
        return null;
    }

    /**
     * @param \UT_Php_Core\Interfaces\IXmlElement $element
     * @return bool
     */
    final public function addChild(\UT_Php_Core\Interfaces\IXmlElement $element): bool
    {
        if ($this -> text === null) {
            $element -> parent = $this -> id;
            $element -> updatePosition($this -> position + 1);
            $this -> children[] = $element;
            return true;
        }
        return false;
    }

    /**
     * @param \UT_Php_Core\Interfaces\IXmlDoctype $doctype
     * @return \UT_Php_Core\Interfaces\IXmlDocument
     */
    final public function asDocument(\UT_Php_Core\Interfaces\IXmlDoctype $doctype = null): \UT_Php_Core\Interfaces\IXmlDocument
    {
        if ($doctype === null) {
            $doctype = Doctype::xml();
        }

        $children = $this -> search(
            '/^' . str_replace('\\', '\\\\', $this -> id) . '$/',
            null,
            self::SEARCH_PARENT,
            false
        );
        $doc = new Document($this -> name, $doctype);
        foreach ($children as $child) {
            $doc -> addChild(clone $child);
        }

        return $doc;
    }

    /**
     * @param  string $element
     * @param  int    $returnIndex default null
     * @param  string $type        default self::Search_Name
     * @return array|Element|null
     */
    final public function search(
        string $regex,
        int $returnIndex = null,
        string $type = self::SEARCH_NAME,
        $recursive = true,
        $recursivePos = 0
    ): ?array {
        $list = array();
        if ($type == self::SEARCH_ATTRIBUTES) {
            $keys = array_keys($this -> attributes);
            foreach ($keys as $key) {
                if (preg_match($regex, $key)) {
                    $list[] = $this;
                }
            }
        } elseif ($this -> $type != null && preg_match($regex, $this -> $type)) {
            $list[] = $this;
        }
        if ($recursive || (!$recursive && $recursivePos === 0)) {
            foreach ($this -> children as $child) {
                $result = $child -> search($regex, null, $type, $recursive, $recursivePos + 1);
                if ($result !== null) {
                    if (is_array($result)) {
                        $list = array_merge($list, $result);
                    } else {
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
    final public function updatePosition(int $pos): void
    {
        $this -> position = $pos;
        foreach ($this -> children as $child) {
            $child -> updatePosition($pos + 1);
        }
    }
}
