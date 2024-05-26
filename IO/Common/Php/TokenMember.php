<?php

namespace UT_Php_Core\IO\Common\Php;

class TokenMember implements IMember
{
    use TDeclarations;
    use TPhpParser;
    use TAccess;

    /**
     * @var array
     */
    private array $tokens;

    /**
     * @param array $tokens
     * @param array|null $args
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
                return is_array($x) && $x[0] === 317;
            })[1];
    }
}
