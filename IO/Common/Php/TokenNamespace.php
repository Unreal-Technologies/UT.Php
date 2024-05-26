<?php

namespace UT_Php_Core\IO\Common\Php;

class TokenNamespace implements \UT_Php_Core\Interfaces\INamespace
{
    /**
     * @var array
     */
    private array $tokens;

    /**
     * @param array $tokens
     */
    public function __construct(array $tokens)
    {
        $this -> tokens = $tokens;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> firstOrDefault(function ($x) {
                return is_array($x) && in_array($x[0], [313, 316]);
            })[1];
    }

    /**
     * @return string
     * @throws \UT_Php_Core\Exceptions\NotImplementedException
     */
    public function declaration(): string
    {
        return implode('', (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> select(function ($x) {
                if (is_array($x)) {
                    return $x[1];
                }
                return $x;
            })
            -> toArray()) . ';';
    }
}
