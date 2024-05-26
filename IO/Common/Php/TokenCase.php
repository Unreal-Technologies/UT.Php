<?php

namespace UT_Php_Core\IO\Common\Php;

class TokenCase implements ICase
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
}
