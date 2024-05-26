<?php

namespace UT_Php_Core\IO\Common\Php;

class TokenNamespace implements INamespace
{
    use TPhpParser;

    /**
     * @var array
     */
    private array $tokens;

    /**
     * @param array $tokens
     */
    public function __construct(array $tokens, ?array $args = null)
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
}
