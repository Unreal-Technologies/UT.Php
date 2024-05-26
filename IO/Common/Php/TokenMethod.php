<?php

namespace UT_Php_Core\IO\Common\Php;

class TokenMethod implements IMethod
{
    use TAccess;
    
    /**
     * @var array
     */
    private array $tokens;

    /**
     * @var array
     */
    private array $body;

    /**
     * @param array $head
     * @param array|null $body
     */
    public function __construct(array $head, ?array $body = null)
    {
        $this -> tokens = $head;
        $this -> body = $body;
    }

    /**
     * @return string
     */
    public function declaration(): string
    {
        return str_replace([',', '  '], [', ', ' '], implode('', (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> select(function ($x) {
                if (is_array($x)) {
                    if (stristr($x[1], "\n")) {
                        return '';
                    }
                    return $x[1];
                }
                return $x;
            })
            -> toArray()));
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> firstOrDefault(function ($x) {
                return is_array($x) && $x[0] === 313;
            })[1];
    }

    /**
     * @return string
     */
    public function body(): string
    {
        return implode('', (new \UT_Php_Core\Collections\Linq($this -> body))
            -> select(function ($x) {
                if (is_array($x)) {
                    return $x[1];
                }
                return $x;
            })
            -> toArray());
    }
}
