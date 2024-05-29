<?php

namespace UT_Php_Core\IO\Common\Php;

trait TDeclarations
{
    /**
     * @return bool
     */
    public function isAbstract(): bool
    {
        return (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> firstOrDefault(function ($x) {
                return is_array($x) && $x[0] === 358;
            }) !== null;
    }

    /**
     * @return bool
     */
    public function isFinal(): bool
    {
        return (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> firstOrDefault(function ($x) {
                return is_array($x) && $x[0] === 359;
            }) !== null;
    }
}
