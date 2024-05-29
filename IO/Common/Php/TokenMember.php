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
    protected array $tokens;

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
        $res = (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> firstOrDefault(function ($x) {
                return is_array($x) && $x[0] === 317;
            });
        if ($res === null) {
            $res = (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> firstOrDefault(function ($x) {
                return is_array($x) && $x[0] === 313;
            });
        }
        return $res[1];
    }

    /**
     * @return bool
     */
    public function isStatic(): bool
    {
        return (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> firstOrDefault(function ($x) {
                return is_array($x) && $x[0] === 357;
            }) !== null;
    }

    /**
     * @param string $old
     * @param string $new
     * @return void
     */
    public function replace(string $old, string $new): void
    {
        foreach ($this -> tokens as $idx => $token) {
            if (is_array($token) && in_array($token[0], [317, 313]) && $token[1] === $old) {
                $token[1] = $new;
                $this -> tokens[$idx] = $token;
            }
        }
    }
}
