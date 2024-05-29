<?php

namespace UT_Php_Core\IO\Common\Php;

class TokenObject implements IObject
{
    use TDeclarations;
    use TPhpParser;

    /**
     * @var array
     */
    protected array $tokens;

    /**
     * @param array $tokens
     */
    public function __construct(array $tokens, ?array $args = null)
    {
        $this -> tokens = $tokens;
    }

    /**
     * @return string|null
     */
    public function extends(): ?string
    {
        $extends = (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> firstOrDefault(function ($x) {
                return is_array($x) && $x[0] === 373;
            });
        if ($extends === null) {
            return null;
        }

        $pos = array_search($extends, $this -> tokens);
        $segment = array_slice($this -> tokens, $pos);

        return (new \UT_Php_Core\Collections\Linq($segment))
            -> where(function ($x) {
                return is_array($x) && in_array($x[0], [313, 316]);
            })
            -> select(function (array $x) {
                return $x[1];
            })
            -> firstOrDefault();
    }

    /**
     * @return array
     */
    public function implements(): array
    {
        $implements = (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> firstOrDefault(function ($x) {
                return is_array($x) && $x[0] === 374;
            });
        if ($implements === null) {
            return [];
        }

        $pos = array_search($implements, $this -> tokens);
        $segment = array_slice($this -> tokens, $pos);

        return (new \UT_Php_Core\Collections\Linq($segment))
            -> where(function ($x) {
                return is_array($x) && in_array($x[0], [313, 316]);
            })
            -> select(function (array $x) {
                return $x[1];
            })
            -> toArray();
    }

    /**
     * @return bool
     */
    public function isClass(): bool
    {
        return (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> firstOrDefault(function ($x) {
                return is_array($x) && $x[0] === 369;
            }) !== null;
    }

    /**
     * @return bool
     */
    public function isTrait(): bool
    {
        return (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> firstOrDefault(function ($x) {
                return is_array($x) && $x[0] === 370;
            }) !== null;
    }

    /**
     * @return bool
     */
    public function isInterface(): bool
    {
        return (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> firstOrDefault(function ($x) {
                return is_array($x) && $x[0] === 371;
            }) !== null;
    }

    /**
     * @return bool
     */
    public function isEnum(): bool
    {
        return (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> firstOrDefault(function ($x) {
                return is_array($x) && $x[0] === 372;
            }) !== null;
    }
}
